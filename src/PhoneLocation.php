<?php

namespace flyingBird\PhoneLocation;

class PhoneLocation
{
    private $fp;
    private int $indexOffset;
    private int $totalIndex;
    private const INDEX_LEN = 9;

    private const OPERATOR_MAP = [
        1 => '中国移动', 2 => '中国联通', 3 => '中国电信',
        4 => '电信虚拟运营商', 5 => '联通虚拟运营商', 6 => '移动虚拟运营商',
    ];

    public function __construct(?string $datPath = null)
    {
        if ($datPath === null) {
            $datPath = dirname(__DIR__) . '/data/phone.dat';
        }

        $this->fp = fopen($datPath, 'rb');
        if (!$this->fp) {
            throw new \RuntimeException("无法打开数据文件: {$datPath}");
        }

        $header = fread($this->fp, 8);
        $data = unpack('Vversion/Vindex_offset', $header);
        $this->indexOffset = $data['index_offset'];

        $fileSize = filesize($datPath);
        $this->totalIndex = intdiv($fileSize - $this->indexOffset, self::INDEX_LEN);
    }

    public function find(string $phone): ?array
    {
        $prefix = (int) substr($phone, 0, 7);
        if ($prefix < 1000000 || $prefix > 1999999) {
            return null;
        }

        $low = 0;
        $high = $this->totalIndex - 1;
        $recordOffset = null;
        $indexData = null;

        while ($low <= $high) {
            $mid = intdiv($low + $high, 2);
            $offset = $this->indexOffset + $mid * self::INDEX_LEN;

            fseek($this->fp, $offset);
            $indexData = fread($this->fp, self::INDEX_LEN);
            $data = unpack('Vprefix/Vrecord_offset', substr($indexData, 0, 8));

            if ($data['prefix'] === $prefix) {
                $recordOffset = $data['record_offset'];
                break;
            } elseif ($data['prefix'] < $prefix) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }

        if ($recordOffset === null) {
            return null;
        }

        fseek($this->fp, $recordOffset);
        $record = '';
        while (($char = fgetc($this->fp)) !== "\0" && $char !== false) {
            $record .= $char;
        }

        $parts = explode('|', $record);
        if (count($parts) < 4) {
            return null;
        }

        $operatorType = unpack('C', substr($indexData, 8, 1))[1];

        return [
            'province'  => $parts[0],
            'city'      => $parts[1],
            'zip_code'  => $parts[2],
            'area_code' => $parts[3],
            'operator'  => self::OPERATOR_MAP[$operatorType] ?? '未知',
        ];
    }

    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }
}
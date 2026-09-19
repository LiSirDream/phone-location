<?php
namespace flyingBird\PhoneLocation\Tests;

include __DIR__ . '/../src/PhoneLocation.php';

use flyingBird\PhoneLocation\PhoneLocation;

class PhoneLocationTest
{
    private PhoneLocation $phone;

    public function __construct()
    {
        $this->phone = new PhoneLocation();
    }

    public function testFindMobile(): void
    {
        // 测试完整11位手机号
        $result = $this->phone->find('18888888888');
        var_dump($result);
    }

    public function testFindByPrefix(): void
    {
        // 测试7位号段
        $result = $this->phone->find('1380013');
        var_dump($result);
    }

    public function testInvalidPhone(): void
    {
        // 测试无效号段
        $result = $this->phone->find('00000000000');
        var_dump($result);
    }

    public function testShortPhone(): void
    {
        // 测试不足7位
        $result = $this->phone->find('138');
        var_dump($result);
    }
}



$test = new PhoneLocationTest();
$test->testFindByPrefix();
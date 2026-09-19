# Phone Location

手机号归属地查询 PHP 库，基于 `phone.dat` 二进制文件，采用二分查找算法，单次查询耗时约 0.1~0.5ms。

## 安装

```bash
composer require flying-bird/phone-location
```

```php
<?php
require 'vendor/autoload.php';

use flyingBird\PhoneLocation\PhoneLocation;

// 方式一：不传参数，自动使用包内置的 phone.dat（推荐）
$phone = new PhoneLocation();

// 方式二：传入自定义路径，使用自己的数据文件
// $phone = new PhoneLocation('/path/to/your/phone.dat');

$result = $phone->find('18888888888');

if ($result) {
    echo "省份：{$result['province']}\n";
    echo "城市：{$result['city']}\n";
    echo "运营商：{$result['operator']}\n";
}
```
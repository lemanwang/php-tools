# PHP工具包
## 版本兼容性
- 原则上，版本更新不影响 老版本的任何使用。
## 环境要求

* PHP >= 5.4

## 安装

``` sh
$ composer require lemanwang/php-tools -vvv
```

## 使用

``` php
<?php
require_once __DIR__."/vendor/autoload.php";
//示例
use Lemanwang\PhpTools\PhpTools;
use Lemanwang\PhpTools\TestTools;

$phpTools = new PhpTools();
$test = new TestTools();
$phpTools->hello();
$test->hello();
```

## License

[MIT](./LICENSE)

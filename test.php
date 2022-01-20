<?php
/**
 * Created by PhpStorm.
 * User: shiwenyuan
 * Date: 2018/8/2 13341007105@163.com
 * Time: 下午8:56
 */
require_once __DIR__."/vendor/autoload.php";

use Lemanwang\PhpTools\PhpTools;
use Lemanwang\PhpTools\Download;

$phpTools = new PhpTools();
$download = new Download();
$phpTools->hello();
$download->hello();

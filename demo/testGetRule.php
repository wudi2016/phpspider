<?php
// composer下载方式
// 先使用composer命令下载：
// composer require owner888/phpspider
// 引入加载器
//require './vendor/autoload.php';

// GitHub下载方式
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\requests;
use phpspider\core\db;
use phpspider\core\selector;

/* Do NOT delete this comment */
/* 不要删除这段注释 */

//n内容页面url
$url = "http://field.10jqka.com.cn/20171214/c602027256.shtml";
$html = requests::get($url);
// 选择器规则

$selector = "//div[contains(@class,'info-fl fl')]//span[contains(@id,'pubtime_baidu')]";
// 提取结果
$result = selector::select($html, $selector);

echo '--------'.PHP_EOL;
echo $result.PHP_EOL;
echo '--------'.PHP_EOL;

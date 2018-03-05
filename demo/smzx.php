<?php
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\requests;
use phpspider\core\selector;
use phpspider\core\log;
use phpspider\core\db;

$configs = array(
    'name' => '同花顺',
    'tasknum' => 1,
//    'multiserver' => true,
    'log_show' => true,
//    'save_running_state' => true,
//    'queue_config' => array(
//        'host'      => '127.0.0.1',
//        'port'      => 6379,
//        'pass'      => 'wudiredis',
//        'db'        => 5,
//        'prefix'    => 'spider:ths',
//        'timeout'   => 30,
//    ),
    'domains' => array(
        "svmuu.com",
        "dev-test.svmuu.com",
    ),
    'scan_urls' => array(
        //"https://www.svmuu.com/gsxy/gpjs/"
        "http://dev-test.svmuu.com/gsxy/"
    ),
    'list_url_regexes' => array(
        //"http://www.svmuu.com/gsxy/gpjs/?pn=\d+",
        "http://dev-test.svmuu.com/gsxy/gpjs/"
    ),
    'content_url_regexes' => array(
        "http://dev-test.svmuu.com/gsxy/gpjs/\d+.html",
    ),
    'max_try' => 5,
    'max_depth' => 3,
    'db_config' => array(
        'host'  => '127.0.0.1',
        'port'  => 3306,
        'user'  => 'root',
        'pass'  => 'wudimysql',
        'name'  => 'spider',
    ),
    'fields' => array(
        // 标题
        array(
            'name' => "title",
            'selector' => "//div[contains(@class,'gs-details-title')]//h5",
            'required' => true,
        ),
    ),
);

$spider = new phpspider($configs);

$spider->on_start = function($phpspider)
{
    $db_config = $phpspider->get_config("db_config");
    // 数据库连接
    db::set_connect('default', $db_config);
    db::init_mysql();
};

$spider->on_scan_page = function($page, $content, $phpspider)
{
    echo '--------------Scan back-----------------'.PHP_EOL;
    return true;
};
//
$spider->on_list_page = function($page, $content, $phpspider)
{
    if($page['url'] == 'http://dev-test.svmuu.com/gsxy/gpjs/'){

    }

    echo '--------------List back-----------------'.PHP_EOL;
    echo $page['url'];
    echo '--------------List back-----------------'.PHP_EOL;
    // 通知爬虫不再从当前网页中发现待爬url
    return false;
};
//
//$spider->on_content_page = function($page, $content, $phpspider)
//{
//    echo '--------------Content back-----------------'.PHP_EOL;
//    return false;
//};

$spider->on_extract_field = function($fieldname, $data, $page)
{
    return $data;
};

$spider->on_extract_page = function($page, $data)
{
    $sql = "Select Count(*) As `count` From `gushizixun` Where `title`='{$data['title']}'";
    $row = db::get_one($sql);
    if (!$row['count'] && $data['title'])
    {
        db::insert("gushizixun", $data);
    }
    return $data;
};

$spider->start();




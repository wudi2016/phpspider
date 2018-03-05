<?php
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\requests;
use phpspider\core\selector;
use phpspider\core\log;
use phpspider\core\db;

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$configs = array(
    'name' => '同花顺',
    'tasknum' => 3,
//    'multiserver' => true,
    'log_show' => false,
    'save_running_state' => true,
    'queue_config' => array(
        'host'      => '127.0.0.1',
        'port'      => 6379,
        'pass'      => 'wudiredis',
        'db'        => 5,
        'prefix'    => 'spider:ths',
        'timeout'   => 30,
    ),
    'domains' => array(
        "10jqka.com.cn",
        "www.10jqka.com.cn",
        "news.10jqka.com.cn",
        "field.10jqka.com.cn",
        "stock.10jqka.com.cn",
        "yuanchuang.10jqka.com.cn"
    ),
    'scan_urls' => array(
        "http://www.10jqka.com.cn/"
    ),
    'list_url_regexes' => array(
        //"http://open.tool.hexun.com/MongodbNewsService/newsListPageByJson.jsp?id=100235806&s=30&cp=\d+&priority=0&callback=hx_json\d+",
    ),
    'content_url_regexes' => array(
        "http://yuanchuang.10jqka.com.cn/\d+/c\d+.shtml",
        "http://news.10jqka.com.cn/\d+/c\d+.shtml",
        "http://field.10jqka.com.cn/\d+/c\d+.shtml",
        "http://stock.10jqka.com.cn/\d+/c\d+.shtml"
    ),
    'max_try' => 5,
    //'max_depth' => 0,
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
            'selector' => "//div[contains(@class,'main-fl fl')]//h2[contains(@class,'main-title')]",
            'required' => true,
        ),
        // 内容
        array(
            'name' => "content",
            'selector' => "//div[contains(@class,'main-text atc-content')]",
            'required' => true,
        ),
        //来源名称
        array(
            'name' => "sourceName",
            'selector' => "//div[contains(@class,'info-fl fl')]//span[contains(@id,'source_baidu')]//a",
            'required' => true,
        ),
        //来源url
        array(
            'name' => "sourceUrl",
            'selector' => "//div[contains(@class,'info-fl fl')]//span[contains(@id,'source_baidu')]//a/@href",
            'required' => true,
        ),
        // 发布时间
        array(
            'name' => "addtime",
            'selector' => "//div[contains(@class,'info-fl fl')]//span[contains(@id,'pubtime_baidu')]",
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
    return true;
};

//$spider->on_list_page = function($page, $content, $phpspider)
//{
//    log::info('-------list begin---------'.PHP_EOL.'list_callback'.PHP_EOL.'----------list end----------'.PHP_EOL);
//    return false;
//};

//$spider->on_content_page = function($page, $content, $phpspider)
//{
//    log::info('-------content begin---------'.PHP_EOL.$content.PHP_EOL.'----------content end----------'.PHP_EOL);
//    return false;
//};

$spider->on_extract_field = function($fieldname, $data, $page)
{
    if($fieldname == 'sourceName'){
        $data = trim($data);
    }elseif ($fieldname == 'sourceUrl'){
        if(!$data){
            $data = "http://www.10jqka.com.cn/";
        }
    }
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




<?php
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\requests;
use phpspider\core\selector;
use phpspider\core\log;
use phpspider\core\db;


$configs = array(
    'name' => '和讯',
    'tasknum' => 1,
    //'multiserver' => true,
    'log_show' => false,
    'save_running_state' => true,
    'queue_config' => array(
        'host'      => '127.0.0.1',
        'port'      => 6379,
        'pass'      => 'wudiredis',
        'db'        => 5,
        'prefix'    => 'spider:hexun',
        'timeout'   => 30,
    ),
    'domains' => array(
        "hexun.com",
        "stock.hexun.com",
        "open.tool.hexun.com"
    ),
    'scan_urls' => array(
        //"http://stock.hexun.com/broadcast/index.html",
        "http://open.tool.hexun.com/MongodbNewsService/newsListPageByJson.jsp?id=100235806&s=30&cp=1&priority=0&callback=hx_json71513220396922"
    ),
    'list_url_regexes' => array(
        "http://open.tool.hexun.com/MongodbNewsService/newsListPageByJson.jsp?id=100235806&s=30&cp=\d+&priority=0&callback=hx_json\d+",
    ),
    'content_url_regexes' => array(
        "http://stock.hexun.com/\d+-\d+-\d+/\d+.html",
    ),
    'max_try' => 5,
    'max_depth' =>1,
    //'export' => array(
    //'type' => 'db',
    //'table' => 'meinv_content',
    //),
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
            'selector' => "//div[@class='layout mg articleName']//h1",
            'required' => true,
        ),
        // 内容
        array(
            'name' => "content",
            'selector' => "//div[contains(@class,'art_contextBox')]",
            'required' => true,
        ),
        //来源名称
        array(
            'name' => "sourceName",
            'selector' => "//div[contains(@class,'tip fl')]//a",
            'required' => true,
        ),
        //来源url
        array(
            'name' => "sourceUrl",
            'selector' => "//div[contains(@class,'tip fl')]//a/@href",
            'required' => true,
        ),
        // 发布时间
        array(
            'name' => "addtime",
            'selector' => "//div[contains(@class,'tip fl')]//span[contains(@class,'pr20')]",
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

    $url = "http://open.tool.hexun.com/MongodbNewsService/newsListPageByJson.jsp?id=100235806&s=30&cp=1&priority=0&callback=hx_json101513232071556";
    $html = requests::get($url);
    preg_match('#hx_json\d+\((.*?)\)#', $html, $out);
    if(!$out){
        return true;
    }
    $out = trim($out[1]);
    $out = iconv("GB2312","UTF-8//IGNORE",$out);
    $arr = json_decode($out,true);
    $pages = $arr['totalPage'];
    for ($i = 1; $i <= $pages; $i++)
    {
        $phpspider->add_scan_url("http://open.tool.hexun.com/MongodbNewsService/newsListPageByJson.jsp?id=100235806&s=30&cp={$i}&priority=0&callback=hx_json91513230106370");
    }
};

$spider->on_scan_page = function($page, $content, $phpspider)
{
    preg_match('#hx_json\d+\((.*?)\)#', $content, $out);
    if(!$out){
        return true;
    }
    $out = trim($out[1]);
    $arr = json_decode($out,true);
    foreach ($arr['result'] as $v){
        $phpspider->add_url($v['entityurl']);
    }
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



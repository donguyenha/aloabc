<?php
include "curl.php";
include 'simple_html_dom.php';

$m = new \MongoClient();
$collection = $m->selectCollection('alofilm', 'articles_crawler');
$collection->drop();

$curl = new Curl();

$j = 0;
$k = 0;
date_default_timezone_set('asia/ho_chi_minh');
//for ($i = 1; $i <= 367; $i ++) {
for ($i = 1; $i <= 970; $i ++) {
    $j++;
    echo "\nPage $j\n";
    $html = json_decode($curl->get("http://hdonline.vn/frontend/post/ajax?page={$i}&catID="));
    $items = $html->result;
    foreach ($items as $item) {
        $k ++;
        $linkDetail = 'http://hdonline.vn' . $item->url;
        $data = array();
        $data['crawler_id'] = $item->id;
        $data['crawler_link'] = $linkDetail;

        $data['title'] = $item->name;
        $data['description'] = $item->sumary;

        $detailHtml = $curl->get($data['crawler_link']);
        $html = str_get_html($detailHtml);
        $data['content'] = $html->find('#content-news', 0)->innertext;
        $data['cover'] = $item->thumb;

        $data['view_count'] = 0;

        $data['process_status'] = 'public';
        $data['created_date']   = date('Y-m-d H:i:s');
        $data['modified_date']  = date('Y-m-d H:i:s');

        echo $data['crawler_link'] . " -> {$k}\n\n";
        $collection->insert($data);
    }
}
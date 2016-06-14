<?php

include 'simple_html_dom.php';
$html = file_get_html('http://www.phim.media/tim-kiem-phim-the-loai-1.html');

$list = $html->find('.list-film li');
foreach ($list as $film) {
    $cover = $film->find('a img', 0)->src;
    $info = $film->find('.info .name a', 0);

    $filmNameVi = $info->plaintext;
    $filmNameEn = $film->find('.info .name2', 0)->plaintext;
    $filmUrl  = $info->href;

    $htmlDetail = file_get_html($filmUrl);
}
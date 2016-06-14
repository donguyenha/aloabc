<?php
include "curl.php";
include 'simple_html_dom.php';

//$m = new \MongoClient();
//$collection = $m->selectCollection('alofilmtest', 'films_crawler');
//$collection->drop();

$curl = new Curl();
$htmlCheck = $curl->get('http://hdonline.vn');
//$curl->setHeaderXMLHttpRequest();
//echo $curl->get('http://hdonline.vn/frontend/episode/xmlplay?ep=1&fid=7674&token=NzA3ODRhNDY0YjU2NDk2YzZjNTgyZjcyNTk1ODY4NDg0NzMyNWE1NzYyNDg1NzQ5MzI0NTU0MzQ0NTUzMzA2OQ==-1464790200&format=json&_x=0.6428161035491811');
//die;
//echo $htmlCheck;die;
//if(strstr($htmlCheck, "DDoS protection by CloudFlare")){
//    //Get the math calc
//    $math_calc = get_between($htmlCheck, "a.value = ", ";");
//    if($math_calc){
//        //Resolve the math calc
//        $math_result = (int)eval("return ($math_calc);");
//        if(is_numeric($math_result)){
//            $math_result += 11; //Domain lenght (just-dice.com)
//            //Send the CloudFlare's form
//            $getData = "cdn-cgi/l/chk_jschl";
//            $getData .= "?jschl_vc=".get_between($res, 'name="jschl_vc" value="', '"');
//            $getData .= "&jschl_answer=".$math_result;
//            $res = $curl->get($config['general']['siteUrl'].$getData.$getData);
//
//            var_dump($res);die;
//        }
//    }
//}

function get_between($string,$start,$end){
    $string = " ".$string;
    $ini = strpos($string, $start);
    if($ini==0) return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$j = 0;
$filmCount = 0;
$provider = array();
//for ($i = 1; $i <= 367; $i ++) {
for ($i = 1; $i <= 505; $i ++) {
    $j ++;
    echo "\nPage $j\n";
    $html = $curl->get("http://hdonline.vn/danh-sach/phim-moi/trang-{$i}.html");
    $html = str_get_html($html);

    $list = $html->find('#cat_tatca li .tn-bxitem');
    foreach ($list as $film) {
        $filmCount ++;
        $linkDetail = 'http://hdonline.vn' . $film->find('a', 0)->href;
        $poster = $film->find('.bxitem-img img', 0)->src;
        $filmNameVi = trim($film->find('h1.bxitem-txt', 0)->plaintext);

        $filmId = @end(explode('-', $linkDetail));
        $filmId = str_replace('.html', '', $filmId);
        $filmId = current(explode('.', $filmId));
        $htmlDetail = $curl->get($linkDetail);
echo $linkDetail . "\n\n";
        $urlDownload = null;

        $downloadInfo = getLinkFilm($htmlDetail, $filmId, $curl, $token, $time);
        if (is_null($downloadInfo)) {
            file_put_contents('failed1.txt', $linkDetail . '|' . $urlDownload, FILE_APPEND);
            file_put_contents('failed1.txt', "\n", FILE_APPEND);
            echo "\n>>>>{$linkDetail}";
            echo "\n>>>>>>>{$urlDownload}";
            echo "\n>>>>>>>>>>>>>> failed";
            var_dump($provider);
            continue;
        }
        file_put_contents('completed1.txt', $linkDetail . '|' . $downloadInfo['file'] . '|' . $downloadInfo['provider'], FILE_APPEND);
        file_put_contents('completed1.txt', "\n", FILE_APPEND);

        echo $linkDetail . " -> {$filmCount} -> completed\n";
        if (!isset($provider[$downloadInfo['provider']])) {
            $provider[$downloadInfo['provider']] = 0;
        }
        $provider[$downloadInfo['provider']]++;

        /**
         * Lấy thông tin film
         */
        $data = array();
        $data['crawler_link'] = $linkDetail;
        $objectFilmDetail = str_get_html($htmlDetail);
        $filmInfo = $objectFilmDetail->find('ul.filminfo-fields li');
        $data['film_name_vi'] = $filmNameVi;
        foreach ($filmInfo as $info) {
            if (@$info->find('img', 0)->src == 'http://hdonline.vn/template/frontend/images/imdb.png') {
                $data['rating_number'] = trim($info->plaintext);
                continue;
            }
            $texts = explode(':', $info->plaintext);
            $label = trim($texts[0]);
            if ($label == 'Tên Phim') {
                $data['film_name_vi'] = trim($texts[1]);
            }

            if ($label == 'Tên Tiếng Anh') {
                $data['film_name_en'] = trim($texts[1]);
            }

            if ($label == 'Năm sản xuất') {
                $data['year_released'] = (int) trim($texts[1]);
            }

            if ($label == 'Thể loại') {
                $categories = explode(',', trim($texts[1]));
                $data['categories'] = $categories;
            }

            if ($label == 'Quốc gia') {
                $countries = explode(',', trim($texts[1]));
                $data['countries'] = $countries;
            }

            if ($label == 'Thời lượng') {
                $data['duration'] = (int) trim($texts[1]);
            }

            if ($label == 'Đạo diễn') {
                $directors = explode(',', trim($texts[1]));
                $data['directors'] = trim($texts[1]);
            }
        }

        $actors = $objectFilmDetail->find('ul.group-filminfo-ul li');
        $l = 0;
        $actorsExt = array();
        foreach ($actors as $actor) {
            $actorName = trim($actor->find('.media-body a', 0)->plaintext);

            if (isset($actorsExt[$actorName])) {
                continue;
            }

            $data['actors'][$l]['actor_name'] = $actorName;
            $data['actors'][$l]['cover'] = $actor->find('.pull-left img', 0)->src;

            $actorsExt[$actorName] = $actorName;
            $l ++;
        }

        $trailers = $objectFilmDetail->find('.block-movie iframe');
        foreach ($trailers as $trailer) {
            if ($trailer->height == '200px') {
                $data['trailer'] = @end(explode('iframeplay.php?file=', $trailer->src));
            }
        }

        $tags = $objectFilmDetail->find('.tn-tagfin h4 a');
        foreach ($tags as $tag) {
            $data['tags'][] = $tag->plaintext;
        }

        $data['cover'] = $objectFilmDetail->find('meta[property=og:image]', 0)->content;
        $data['description'] = $objectFilmDetail->find('div.tn-contentmt', 0)->children(0)->outertext;

        $filesHtml = $curl->get('http://hdonline.vn/episode/ajax?film=' . $filmId);

        $objectFilesHtml = str_get_html($filesHtml);
        $files = $objectFilesHtml->find('a.btn-episode');
        $data['file'] = array();
        if (count($files)) {
            $k = 1;
            foreach ($files as $file) {
                $urlDownload = "http://hdonline.vn/frontend/episode/xmlplay?ep={$k}&fid={$filmId}&token={$token}-{$time}&format=json&_x=0.8413578739490713";
                $downloadInfo = json_decode($curl->get($urlDownload), true);
                if (!is_null($downloadInfo)) {
                    $data['file'][] = $downloadInfo['file'];
                }
                $k ++;
            }
        }

        if (count($data['file']) == 1) {
            $data['film_type'] = 'odd';
        } else {
            $data['film_type'] = 'series';
        }
        $data['poster'] = $poster;
        $data['crawler_film_id'] = $filmId;

        $filmQuality = $film->find('.tip-info-bottom li');
        $data['subtitle_types'] = array();
        foreach ($filmQuality as $quality) {
            $spanObj = $quality->find('span', 0);
            if ($spanObj->title == 'Phụ đề Việt') {
                $data['subtitle_types'][] = 'vi';
            } elseif ($spanObj->title == 'Phụ đề Anh') {
                $data['subtitle_types'][] = 'en';
            } elseif ($spanObj->title == 'Thuyết minh') {
                $data['subtitle_types'][] = 'present';
            } elseif ($spanObj->title == 'Chất lượng HD 720p') {
                $data['quality'] = 'hd';
            }
        }

        $collection->insert($data);
    }
}

function getLinkFilm($html, $filmId, $curl, &$token, &$time) {
    if (strpos($html, '-1464')) {
        preg_match("/(\&token\=)(.*)(\",\"vplugin.host\")/",
            $html,
            $matches);
        $matches[2] = current(explode('","', $matches[2]));
        $token = current(explode(".split('", $matches[2]));
        $tokens = explode('-', $token);
        $token = $tokens[0];
        $time = $tokens[1];
        $urlDownload = "http://hdonline.vn/frontend/episode/xmlplay?ep=1&fid={$filmId}&token={$token}-{$time}&format=json&_x=0.8649589051258331";
    } else {
        preg_match("/(eval\(function\(p,a,c,k,e,d\)\{e)(.*)(split\(\'\|\'\)\,0\,\{\}\)\))/",
            $html,
            $matches);
        $htmlTokens = explode('|', $matches[0]);
        $token = null;
        $time = null;
        foreach ($htmlTokens as $htmlToken) {
            if (strlen($htmlToken) == 96 || strlen($htmlToken) == 86) {
                $token = $htmlToken;
            }

            if (strpos($htmlToken, '1464') !== false) {
                $time = $htmlToken;
            }
        }

        if (!is_null($token) && !is_null($time)) {
            $urlDownload = "http://hdonline.vn/frontend/episode/xmlplay?ep=1&fid={$filmId}&token={$token}-{$time}&format=json&_x=0.8649589051258331";
        }
    }

    echo $urlDownload . "\n\n";
    echo $curl->get($urlDownload);
    return json_decode($curl->get($urlDownload), true);
}

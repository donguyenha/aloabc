<?php
/*
Made by Kudusch (blog.kudusch.de, kudusch.de, @Kudusch)

---------

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>
Everyone is permitted to copy and distribute verbatim or modified
copies of this license document, and changing it is allowed as long
as the name is changed.

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

0. You just DO WHAT THE FUCK YOU WANT TO.

---------

# How to use

- go to http://mediathek.daserste.de, open with iPad User Agent
- navigate to the desired program, get link to the master.m3u8 file from source (in the <video>-tag)
- run the script with url to the master.m3u8 file as argument (e.g. php get_tshls 'http://http://hls.daserste.de/i/videoportal/Film/c_380000/386059/ios.smil/master.m3u8'
- wait for the script to download and merge all media files; every 10 second part is about 3 MB (when it's done, it will output the runtime)
- if necessary, convert the *.ts file with a media converter eg. handbrake to a *.mp4 file
- enjoy

*/
define('get_as_float', true);
//runtime
$startTime = microtime(get_as_float);

//get url from input
//$url = $argv[1];
$url = 'http://s8.phimhd3s.com/f99628a9c956eb5b36c6df9ca100d6cd/phimbo/trungquoc/2016/Toi_Va_Tuoi_17_Cua_Toi_2016/Toi_Va_Tuoi_17_Cua_Toi_2016_720p_H_E001/list.m3u8';
//get stream with highest bandwith
$streamUrl = getHighBandwidthStream($url);
//get array of all links to *.ts files
$list = getHlsFiles($streamUrl);

//make new directory
if (!is_dir('files')) {
    mkdir('files');
}

//download all files from array, name with 3 leading zeros
//if file is longer than 166.5 minutes, adjust str_pad params
$n = 1;
foreach ($list as $key) {
    $number = str_pad($n, 3, "0", STR_PAD_LEFT);
    print_r($n." ");
    file_put_contents("files/part.".$number.".ts", fopen($key, 'r'));
    $n++;
}

//merge files and delte parts
sleep(10);
mergeFiles('files');

//echo part numbers and runtime for debugging
echo("\nRun in ".(microtime(get_as_float)-$startTime)." seconds.");

//input: string, output: string
function getHighBandwidthStream($masterUrl) {
    //get content of master.m3u8
    $ch = curl_init($masterUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    //return link to second last stream (https://developer.apple.com/library/ios/documentation/networkinginternet/conceptual/streamingmediaguide/FrequentlyAskedQuestions/FrequentlyAskedQuestions.html#//apple_ref/doc/uid/TP40008332-CH103-SW1)
    $result = explode("#", $result);
    for ($i = 0; $i < 2; $i++) {
        array_shift($result);
    }
    $length = count($result);
    $result = explode("\n", $result[$length-2]);
    return $result[1];
}

//input: string, output: array
function getHlsFiles($streamUrl) {
    //get content of *.m3u8 file
    $ch = curl_init($streamUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $raw = curl_exec($ch);
    curl_close($ch);

    //remove comments and unnecessary data
    $list_raw = explode("\n", $raw);
    for ($i = 0; $i < 5; $i++) {
        array_shift($list_raw);
    }
    for ($i = 0; $i < 2; $i++) {
        array_pop($list_raw);
    }

    //extract file links
    $list = array();
    $i = 1;
    foreach ($list_raw as $key) {
        if($i%2 == 0) {
            array_push($list, $key);
        }
        $i++;
    }

    //return array
    return $list;
}

function mergeFiles($dirName) {
    //get all *.ts files in directory
    if ($handle = opendir($dirName)) {
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, ".ts") !== false) {
                $fileList = $fileList." files/".$file;
            }
        }
        closedir($handle);
    }

    //join and remove parts
    $shellScript = "cat ".substr($fileList, 1)." >> movie.ts";
    shell_exec($shellScript);
    shell_exec("rm -r files");
}
?>
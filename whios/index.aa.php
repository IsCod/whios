<?php


require dirname(__FILE__) . '/vendor/autoload.php';

use phpWhois\Whois;

$whois = new Whois();
$strs = range('a', 'z');
//$strs = ['i'];
$head = '';
$suffix = ['.com', '.cn'];
$min_length = 5;
$max_length = 6;

foreach ($strs as $s1) {
    foreach ($strs as $s2) {
//        foreach ($strs as $s3) {
//            foreach ($strs as $s4) {
//                foreach ($strs as $s5) {
                    $domain = 'tao' . $s1  . $s2 . '.com';
                    $result = $whois->lookup($domain);
                    echo $domain . ': ' . $result['regrinfo']['registered'];
                    if ($result['regrinfo']['registered'] == 'no') {
                        echo " ====== use register";
                        $rawdata = getRaw($result['rawdata']);
                    }
                    echo "\n" ;
//                }
//            }
//        }
    }
}

foreach ($strs as $s) {
    $domain = $head . $s;
    $domain .= '.com';
    $result = $whois->lookup($domain);
    if ($result['regrinfo']['registered'] == 'no') {
        echo $domain;
        $rawdata = getRaw($result['rawdata']);
        var_dump($rawdata);
    }
}

function getRaw($rawdata) {
    $return = [];
    foreach ($rawdata as $str) {
        $arr =  preg_split("/: +/", $str);

        $key = $arr[0] ?? '';
        $value = $arr[1] ?? '';
        if ($key) {
            $is_expir = stripos($key, 'Expir');
            if ($is_expir !== false) {
                $data = date('Y-m-d H:i:s', strtotime($value));
                if ($data > '1970-01-01 00:00:00') {
                    $return['ExpiryDate'] = $data;
                }

            }

//            $is_expir = stripos($key, 'Creation');
//            if ($is_expir !== false) {
//                $return['CreationDate'] = date('Y-m-d H:i:s', strtotime($value));
//            }
        }
    }
    return $return;
}

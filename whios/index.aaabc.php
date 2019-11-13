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
        foreach ($strs as $s3) {
            // foreach ($strs as $s4) {
//                foreach ($strs as $s5) {
                    $domain = $s1 . $s1 . $s1 . $s2 . $s3 . '.com';
                    $result = $whois->lookup($domain);
                    $status = getStatus($result);
                    echo $domain . ': ' . $status;
                    if ($status == 'no') {
                        echo " ====== use register";
                        $price = getPrice($domain);
                        echo "\tprice : " . $price;
                    }
                    echo "\n" ;
                // }
//            }
        }
    }
}
die();

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

function getStatus($data)
{

    if (in_array($data['regrinfo']['registered'], ['no', 'yes'])) {
        return $data['regrinfo']['registered'];
    }

    foreach ($data['rawdata'] as $str) {
        $arr = preg_split("/: +/", $str);

        $key = $arr[0] ?? '';
        $value = $arr[1] ?? '';
        $value = trim($value);
        if ($key) {
            $is_expir = stripos($key, 'Domain Status');
            if ($is_expir !== false) {
                if (in_array($value, ['inactive'])) {
                    return 'yes';
                }
                if (in_array($value, ['no', 'yes'])) {
                    return $value;
                }
            }
        }
    }

    $expirt_time = getExpirationTime($data);
    if ($expirt_time) {
        if ($expirt_time < date('Y-m-d H:i:s')) {
            return "no";
        } else {
            return "yes";
        }
    } else {
        return false;
    }
}

function getExpirationTime($data)
{
    $rawdata = $data['rawdata'];

    foreach ($rawdata as $str) {
        $arr = preg_split("/: +/", $str);

        $key = $arr[0] ?? '';
        $value = $arr[1] ?? '';
        if ($key) {
            $is_expir = stripos($key, 'Expir');
            if ($is_expir !== false) {
                $data = date('Y-m-d H:i:s', strtotime($value));
                if ($data > '1970-01-01 00:00:00') {
                    return $data;
                }

            }
        }
    }
    return false;
}

function Rand_IP()
{
    $ip2id = round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
    $ip3id = round(rand(600000, 2550000) / 10000);
    $ip4id = round(rand(600000, 2550000) / 10000);
    //下面是第二种方法，在以下数据中随机抽取
    $arr_1 = array("218", "218", "66", "66", "218", "218", "60", "60", "202", "204", "66", "66", "66", "59", "61", "60", "222", "221", "66", "59", "60", "60", "66", "218", "218", "62", "63", "64", "66", "66", "122", "211");
    $randarr = mt_rand(0, count($arr_1) - 1);
    $ip1id = $arr_1[$randarr];
    return $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
}

function getPrice($domain) {
    $ip = Rand_IP();

    $headers = [
        'Accept: application/json, text/plain',
        'Origin: https://sg.godaddy.com',
        // 'Referer: https://sg.godaddy.com/zh/domain-value-appraisal/appraisal/?isc=cjc999com&checkAvail=1&tmskey=&domainToCheck=c' . $domain,
        'Sec-Fetch-Mode: cors',
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36',
        // 'X-DataCenter: PHX3',
        // 'X-Request-Id: ' . md5($Domain)
    ];

    $price = do_request($domain, [], false, $headers);
    return $price['govalue'] ?? '';
}

function do_request($domain, $params = [], $is_post = false, $headers = []){
    $url = "https://api.godaddy.com/v1/appraisal/" . $domain;

    if (!$is_post && !$is_put) {
        if ($params) {
            $p_str = '';
            $comma = '';
            foreach ($params as $k => $v) {
                $p_str .= $comma . $k . '=' . $v;
                $comma = '&';
            }

            $url = $url . '?' . $p_str;
        }
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    if (!empty($headers) && is_array($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    if ($is_post) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
    }
    if ($is_put) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    }

    $output = curl_exec($ch);
    $error = curl_error($ch);

    if ($output === FALSE) {
        // error log
        print_r($error);
        // die();
    }
    curl_close($ch);

    $rs = json_decode($output, true) ?? $output;

    return $rs;
}

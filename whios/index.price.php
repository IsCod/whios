<?php

require dirname(__FILE__) . '/index.domains.php';

$domains = array_unique($domains);

foreach ($domains as $domain) {
    echo $domain;

    $price = getPrice($domain, 'USD');
    $price_cn = getPrice($domain, 'RMB');
    echo "\tprice, USD : " . $price . "\t RMB : " . $price_cn;
    echo "\n";
}
die();

function Rand_IP()
{
    $ip2id = round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
    $ip3id = round(rand(600000, 2550000) / 10000);
    $ip4id = round(rand(600000, 2550000) / 10000);
    //下面是第二种方法，在以下数据中随机抽取
    $arr_1 = array(
        "218",
        "218",
        "66",
        "66",
        "218",
        "218",
        "60",
        "60",
        "202",
        "204",
        "66",
        "66",
        "66",
        "59",
        "61",
        "60",
        "222",
        "221",
        "66",
        "59",
        "60",
        "60",
        "66",
        "218",
        "218",
        "62",
        "63",
        "64",
        "66",
        "66",
        "122",
        "211"
    );
    $randarr = mt_rand(0, count($arr_1) - 1);
    $ip1id = $arr_1[$randarr];
    return $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
}

function getPrice($domain, $currency = 'USD')
{
    $ip = Rand_IP();

    if($currency == 'USD') {
        $headers = [
            'Accept: application/json, text/plain',
            'Origin: https://sg.godaddy.com',
            // 'Referer: https://sg.godaddy.com/zh/domain-value-appraisal/appraisal/?isc=cjc999com&checkAvail=1&tmskey=&domainToCheck=c' . $domain,
            'Sec-Fetch-Mode: cors',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36',
            // 'X-DataCenter: PHX3',
            // 'X-Request-Id: ' . md5($Domain)
        ];

        $url = "https://api.godaddy.com/v1/appraisal/" . $domain;

        $price = do_request($url, [], false, $headers);
        $return = $price['govalue'] ?? '';

        if (!$return) {
            sleep(5);
            return getPrice($domain);
        }
    }

    if ($currency = 'RMB') {
        $url = 'http://www.wanmi.cc/gj/' . $domain;
        $output = do_request($url, [], false, []);
        $regex4="/<div class=\"gujia\".*?>.*?<\/div>/ism"; 
        if(preg_match_all($regex4, $output, $matches)){ 
            preg_match('/(¥)(.*)(元)/', $matches[0][0], $return, PREG_OFFSET_CAPTURE);
            $return = $return[2][0] ?? '0';
        }else{ 
           $return = '0'; 
        }
    }
    return trim($return);
}

function do_request($url, $params = [], $is_post = false, $headers = [], $is_put = false)
{
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

    if ($output === false) {
        // error log
        // print_r($error);
        // die();
    }
    curl_close($ch);

    $rs = json_decode($output, true) ?? $output;

    return $rs;
}

<?php
require dirname(__FILE__) . '/dingtalk.php';

start();

function start(){
    $html = gethtml();
    $pattern = '/>([a-z]{4})\.com<\/a>/';
    preg_match_all($pattern, $html, $matches);
    $ju_domains = $matches[1] ?? [];
    $ju_domains = array_unique($ju_domains);

    $html = getenameHtml();
    $pattern = '/<span>([a-z]{4})\.com<\/span>/';
    preg_match_all($pattern, $html, $matches);
    $en_domains = $matches[1] ?? [];
    $domains = array_merge($ju_domains, $en_domains);

    $amHtml = getamHtml();
    $amHtml = json_decode($amHtml, true);

    foreach ($amHtml['data'] as $key => $value) {
        $domains[] = str_replace(".com", '', $value['Domain']);
    }

    $domains = array_unique($domains);

    foreach ($domains as $domain) {
        $domain .= ".com";
        echo $domain;

        $price_cn = getPrice($domain, 'RMB');
        $price_cn = str_replace(',', '', $price_cn);
        echo "\tprice, RMB : " . $price_cn;
        if ($price_cn >= 2100) {
            $price = getPrice($domain, 'USD');
            echo "\tprice, USD : " . $price;
            if ($price_cn >= 5000) {
                $send_msg = $domain . " USD: " . $price . " RMB: " . $price_cn; 
                sendDingTalk($send_msg);
            }else{
                if ($price >= 1500 and $price_cn >= 2000) {
                    $send_msg = $domain . " USD: " . $price . " RMB: " . $price_cn; 
                    sendDingTalk($send_msg);
                }
            }
        }

        echo "\n";
    }

    sleep(60);
}


function gethtml(){

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://www.juming.com/ykj/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "ymlx=53&ymhz=com&api_sou=1&jgpx=1&meiye=100",
    CURLOPT_HTTPHEADER => array(
      "Cache-Control: no-cache",
      "Content-Type: application/x-www-form-urlencoded",
      "Postman-Token: d142bddb-9afc-14fc-d925-8a6fe93f7a89"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
    die();
  } else {
    return html_entity_decode($response);
  }
}

function getamHtml() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://am.22.cn/ajax/yikoujia/default.ashx?t=0.03460796503984187",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "ddlSuf=.com&ddlclass=11&orderby=Price_a&pageCount=150",
      CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Content-Type: application/x-www-form-urlencoded",
        "Postman-Token: 250e699b-ca32-d557-43b0-72c95b2b02b5"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
      die();
    } else {
      return $response;
    }
}

function getenameHtml(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://auction.ename.com/tao/buynow?domain=2&domainsld=&transtype=1&sort=2&bidpricestart=0&bidpriceend=&skipword1=&domaingroup=11&domaintld[0]=1&domainlenstart=1&domainlenend=&shopUid=&registrar=0&regtime=0&finishtime=0&exptime=0&current=yikoujia&pageSize=100&name=&domaintld%5B0%5D=1",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Postman-Token: 1adf6597-e07d-e542-f6d0-4e9439f4ca56",
        "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
      die();
    } else {
      return $response;
    }
}

function strToUtf8($str){
    $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
    if($encode == 'UTF-8'){
        return $str;
    }else{
        return mb_convert_encoding($str, 'UTF-8', $encode);
    }
}

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
            return getPrice($domain, $currency);
        }
    }

    if ($currency == 'RMB') {
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
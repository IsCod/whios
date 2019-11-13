<?php


require dirname(__FILE__) . '/vendor/autoload.php';

use phpWhois\Whois;

$whois = new Whois();
$strs = range('a', 'z');
$head = '';
$suffix = ['com', 'cn'];
$min_length = 5;
$max_length = 6;

foreach ($strs as $s1) {
    foreach ($strs as $s2) {
        foreach ($strs as $s3) {
            foreach ($strs as $s4) {
//                foreach ($strs as $s5) {
                $arr = [];
                $domain = $s1 . $s2 . $s3 . $s4 ;
                foreach ($suffix as $suf) {
                    $domain_suf = $domain . '.' . $suf;

                    $result = $whois->lookup($domain_suf);
                    $status = getStatus($result);
                    if (!$status) {
                        //在尝试一次
                        $result = $whois->lookup($domain_suf);
                        $status = getStatus($result);
                    }
                    $arr[$suf] = $status;
                    $exps[$suf] = getExpirationTime($result);
                }


//                if ($arr['com'] != $arr['cn'] || $arr['com'] == 'no') {
                    echo $domain . ":\t";
                    foreach ($arr as $key => $value) {
                        echo $key . ": " . $value . "\t";
                    }

                    echo " ExpirationTime ";

                    foreach ($exps as $key => $value) {
                        echo  $key . ": " . $value . "\t";
                    }
                    echo "\n";
//                }
            }
//            }
        }
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


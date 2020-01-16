<?php 

function sendDingTalk(string $content, bool $at = false, $access_token = "05538a32e1d0b0ca72e0659aadc846b8bba4176365cc7d918a0bd316ed04c554"){
    $message = [
        "msgtype" => "text",
        "text" => ["content" => $content]
    ];

    if ($at) {
    	$message["at"] = ["atMobiles" => [], "isAtAll"=> true];
    }

    $curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://oapi.dingtalk.com/robot/send?access_token=" . $access_token,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_HEADER => "",
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($message),
		CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		return false;
	  // echo "cURL Error #:" . $err;
	} else {
		$response = json_decode($response, true);
		return $response['errcode'] == 0;
	  // echo $response;
	}
	return false;
}
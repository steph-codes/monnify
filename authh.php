<?php
  $url = "https://sandbox.monnify.com/api/v1/auth/login";
  
  $auth= base64_encode('MK_TEST_Q38EGU9DYL:7E4UTMSCHNXDPUFK2ESNUG3HGDFRLHYY');

  $headers = [
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic '.$auth //add space after word basic
  ];
  //open connection
  $ch = curl_init();
  
  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS,true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  //So that curl_exec returns the contents of the cURL; rather than echoing it
  $output = curl_exec($curl);
    $result = json_decode($output);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit;

    $error_msg = curl_error($curl);
    if($error_msg){
        echo $err_msg;
    }

    curl_close($curl);

    if($result->responseCode == 0){
        $response = [
             '$res' => $result->responseBody->accessToken
        ];
        return $response;
    }

?>


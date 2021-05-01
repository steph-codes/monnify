<?php
//extract data from the post
//extract($_POST);

//set POST variables
$url = "https://sandbox.monnify.com/api/v1/auth/login";
$fields = array(
            'username' => 'MK_TEST_Q38EGU9DYL',
            'password' => '7E4UTMSCHNXDPUFK2ESNUG3HGDFRLHYY',
            
        );

//url-ify the data for the POST
$fields_string = http_build_query($fields);

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//execute post
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
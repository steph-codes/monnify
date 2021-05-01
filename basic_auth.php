

<?php
$login = 'MK_TEST_Q38EGU9DYL';
$password = '7E4UTMSCHNXDPUFK2ESNUG3HGDFRLHYY';
$url = 'https://sandbox.monnify.com/api/v1/auth/login';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
$output = curl_exec($ch);
$result = json_decode($output);
curl_close($ch);  

echo "<pre>";
print_r($result);
echo "</pre>";

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

 <?php
 
      $url = "https://sandbox.monnify.com/api/v1/auth/login";

      $auth= base64_encode('MK_TEST_NZM4B5XMUA:7DHAK25QL7UA2NGAYDNZVATGEXQRW4JK');

      var_dump($auth);

      $headers = array(
          'Accept: application/json',
          'Authorization: Basic '.$auth
      );

     var_dump($headers);

     $curl = curl_init();

     curl_setopt_array($curl, array(
         CURLOPT_URL => 'https://sandbox.monnify.com/api/v1/auth/login/',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER => $headers,
     ));

     $response = curl_exec($curl);
     $result = json_decode($response);

     $error_msg = curl_error($curl);
     if($error_msg){
         var_dump($error_msg);
     }

     curl_close($curl);

     if($result->responseCode == 0){
         $response = [
              'accessToken' => $result->responseBody->accessToken
         ];
         var_dump($response);
     }

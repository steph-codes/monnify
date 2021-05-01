<?php


  $url = 'https://sandbox.monnify.com/api/v1/invoice/create';
  $invoice_no = rand(1000000000, 9999999999);
  $fields = [
      "amount"=>$amount,
      "invoiceReference"=>$invoice_no,
      "description"=>"test invoice",
      "currencyCode"=>"NGN",
      "contractCode"=>"6812976159",
      "customerEmail"=>"tech-support@novajii.com",
      "customerName"=>$customer_name,
      "expiryDate"=> date('Y-m-d h:i:s',time() + 10),
      "paymentMethods"=>"ACCOUNT_TRANSFER",
  ];

  $authorization = 'TUtfVEVTVF9RMzhFR1U5RFk6N0U0VVRNU0NITlhEUFVGSzJFU05VRzNIR0RGUkxIWVk=';

  $headers = [
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Bearer '.$authorization
  ];

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


  $output = curl_exec($curl);
  $result = json_decode($output);
  var_dump($result); exit;

  $err = curl_error($curl);
  if($err){
      echo "error: ".$err;
  }

  curl_close($curl);

  if($result->responseCode == 0){
      $response = [
          'ref' => $invoice_no,
          'acc_no' => $result->responseBody->accountNumber
      ];
      return $response;
  }



?>
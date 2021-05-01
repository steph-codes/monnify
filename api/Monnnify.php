<?php

    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    
    //echo'soemthing';    
if(isset($_GET['bank_name']) && isset($_GET['amount']) && isset($_GET['phone']) && isset($_GET['customer_name'])){
    $bank_name =$_GET['bank_name'];
    $amount =$_GET['amount'];
    $phone = $_GET['phone'];
    $customer_name = $_GET['customer_name'];
}
index('Access bank', '500', '8107282467', 'Tobi');


function index($bank_name, $amount, $phone, $customer_name){
    $acc_num = get_invoice($amount, $phone, $customer_name);
    $details = get_details($bank_name, $amount, $acc_num['acc_no']);

    if($details){
        $response = [
            'status_code'=> 0,
            'status'=>'success',
            'customer_name'=> $customer_name,
            'reference'=>$acc_num['ref'],
            'phone'=>$phone,
            'ussdTemplate'=>$details
        ];
        return json_encode($response);
    }else{
        $response = [
            'status_code'=> 1,
            'status'=>'failed',
        ];
        return json_encode($response);
    }
    

}


function get_details($bank_name, $amount, $Accountnumber){
        $handle = curl_init();
        $url = 'https://sandbox.monnify.com/api/v1/sdk/transactions/banks';

        
        $authorization = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOlsibW9ubmlmeS1wYXltZW50LWVuZ2luZSJdLCJzY29wZSI6WyJwcm9maWxlIl0sImV4cCI6MTYxODg5OTMxOCwiYXV0aG9yaXRpZXMiOlsiTVBFX01BTkFHRV9MSU1JVF9QUk9GSUxFIiwiTVBFX1VQREFURV9SRVNFUlZFRF9BQ0NPVU5UIiwiTVBFX0lOSVRJQUxJWkVfUEFZTUVOVCIsIk1QRV9SRVNFUlZFX0FDQ09VTlQiLCJNUEVfQ0FOX1JFVFJJRVZFX1RSQU5TQUNUSU9OIiwiTVBFX1JFVFJJRVZFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfREVMRVRFX1JFU0VSVkVEX0FDQ09VTlQiLCJNUEVfUkVUUklFVkVfUkVTRVJWRURfQUNDT1VOVF9UUkFOU0FDVElPTlMiXSwianRpIjoiMjliNzU3OGUtOWI1Zi00YmNhLTg3ZjYtMDZiZGUxNGJiOTM2IiwiY2xpZW50X2lkIjoiTUtfVEVTVF9RMzhFR1U5RFlMIn0.En1gdVTJM3vfgE4WuSfGWXTbk28ntaOIdMIPlJMvlOwdH9IwD7wFeDlkwuD9Vcf7JBeoze_e3e9__TY0KFoZcjhbW0t7sb4K93eiDOX9QKxqK2caf9H-MlECtXnDcXsBDNAQlld03kTCNeV_xEGVGGQ54dYzjQyKVdakL4rnTzg-8u2i0nrELlXFnwH8ySUzzfEGTlH-cWBA-MW86gD0K-7r7efshN-ucZk6wrYgab4e0DS7nNT1IvljKkOm1seKLbEOXoeldECYKmmtl__mx7k880lLQ4fRK9SFjEkCydqQOMHUEGzyWBcHoYWzPUO7RoHCZT4GXt8uqVdY5XGczw";


        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '.$authorization
        ];
         
        // Set the url
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        // Set the result output to be a string.
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

         
        $output = curl_exec($handle);

        $result = json_decode($output);
        $error =  curl_error($handle);
        if($error){
            echo "error: ".$error;
        }
        curl_close($handle);
         
        echo $output;
        var_dump($result->responseBody);
        //get USSD code
        $bank_codes = $result->responseBody;
        foreach ($bank_codes as $bankcode) {
            if ($bankcode['name'] == $bank_name){
                $temp = $bankcode['ussdTemplate'];

                $first = str_replace("Amount",$amount,$temp);

                $strr = str_replace("AccountNumber",$Accountnumber,$first);
                return $strr;
            }
        }
}

function get_invoice($amount, $phone, $customer_name){
    $url = 'https://sandbox.monnify.com/api/v1/invoice/create';

    $invoice_no = rand(1000000000, 9999999999);
    $fields = [
        "amount"=>$amount,
        "invoiceReference"=>$invoice_no,
        "invoiceStatus"=>"PENDING",
        "description"=>"test invoice",
        "currencyCode"=>"NGN",
        "contractCode"=>"6812976159",
        "customerEmail"=>"tech-support@novajii.com",
        "customerName"=>$customer_name,
        "expiryDate"=> date('Y-m-d h:i:s',time() + 10),
        "paymentMethods"=>"ACCOUNT_TRANSFER",
        "apiKey"=>"MK_TEST_Q38EGU9DYL"
    ];

    $authorization = '';
    
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);


    $output = curl_exec($curl);
    $result = json_decode($output);
    // var_dump($result); exit;

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

}



?>


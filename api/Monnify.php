<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    //echo'soemthing';    
if(isset($_POST['bank_name']) && isset($_POST['amount']) && isset($_POST['phone']) && isset($_POST['customer_name'])){
    $bank_name =$_POST['bank_name'];
    $amount =$_POST['amount'];
    $phone = $_POST['phone'];
    $customer_name = $_POST['customer_name'];
}
index($bank_name, $amount, $phone, $customer_name);


function index($bank_name, $amount, $phone, $customer_name){
    $acc_num = get_invoice($amount, $phone, $customer_name); 
    $details = get_details($bank_name, $amount, $acc_num['acc_no']);
    
    if(isset($details->status_code) == 1){
        $response = [
            'status_code'=> 1,
            'status'=>'failed',
        ];
        echo json_encode($response);
        return json_encode($response);
    }
    

    if($details){
        $response = [
            'status_code'=> 0,
            'status'=>'success',
            'customer_name'=> $customer_name,
            'reference'=>$acc_num['ref'],
            'phone'=>$phone,
            'ussdTemplate'=>$details
        ];

        echo json_encode($response);
        return json_encode($response);
    }else{
        $response = [
            'status_code'=> 1,
            'status'=>'failed',
        ];
        echo json_encode($response);
        return json_encode($response);
    }
    

}


function get_details($bank_name, $amount, $Accountnumber){
        $handle = curl_init();
        $url = 'https://sandbox.monnify.com/api/v1/sdk/transactions/banks';

        $authorization = authenticate();
        

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
        

        if($result->responseBody){
            //get USSD code
            $bank_codes = $result->responseBody;
            foreach ($bank_codes as $bankcode) {
                if (strtolower($bankcode->name) == strtolower($bank_name)){
                    $temp = $bankcode->ussdTemplate;
    
                    $first = str_replace("Amount",$amount,$temp);
    
                    $strr = str_replace("AccountNumber",$Accountnumber,$first);
                    return $strr;
                }
            }
        }

        $response = [
            'status_code'=> 1,
            'status'=>'failed',
        ];
        return json_encode($response);
        
      
}

function get_invoice($amount, $phone, $customer_name){
    $url = 'https://sandbox.monnify.com/api/v1/invoice/create';
    $invoice_no = rand(1000000000, 9999999999);
    $fields = [
        "amount"=>$amount,
        "invoiceReference"=>$invoice_no,
        "description"=>"test invoice",
        "currencyCode"=>"NGN",
        "contractCode"=>"7415431651",
        "customerEmail"=>"tech-support@novajii.com",
        "customerName"=>$customer_name,
        "expiryDate"=> date('Y-m-d h:i:s',time() + 10)
    ];

    $authorization = base64_encode('MK_TEST_Q38EGU9DYL:7E4UTMSCHNXDPUFK2ESNUG3HGDFRLHYY');


    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic '.$authorization
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
    }else{
        $response = [
            'status_code'=> 1,
            'status'=>'failed',
        ];
        return json_encode($response);
    }

}

function authenticate(){
    $handle = curl_init();
    $url = 'https://sandbox.monnify.com/api/v1/auth/login';

    $authorization = base64_encode('MK_TEST_Q38EGU9DYL:7E4UTMSCHNXDPUFK2ESNUG3HGDFRLHYY');

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic '.$authorization
    ];
     
    // Set the url
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_POST, true);
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

    if($result){
        $response = $result->responseBody->accessToken;
        return $response;
    }
}

?>
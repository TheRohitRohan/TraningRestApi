<?php
require_once('function.php');
$request_method = $_SERVER["REQUEST_METHOD"];

if($request_method == "GET") {
    if (isset($_GET['id'])){
        $customer_list = getCustomerFromId($_GET);
    }
    else{
        $customer_list = getCustomerList();
    }
    echo $customer_list;
}
elseif($request_method == "POST") {
    $store_customer = storeCustomer($_POST);
    echo $store_customer;
    // echo json_encode($_POST);
}
elseif($request_method == "PUT") {
    $input_data = json_decode(file_get_contents("php://input"), true);
    $update_customer = updateCustomer($input_data);
    echo $update_customer;
}
elseif($request_method == "DELETE") {
    $delete_customer = deleteCustomer($_GET);
    echo $delete_customer;

}
else {
    $data = [
        'status' => 405,
        'message' => $request_method . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
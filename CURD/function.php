<?php
error_reporting(0);
$conn = mysqli_connect("localhost", "root", "", "demo");
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

function getErrorStatement($status, $message) {
    $data = [
        'status' => $status,
        'message' => $message,
    ];
    return json_encode($data);
}
function getCustomerList()
{
    global $conn;

    $query = "SELECT * FROM customers";
    $query_run = mysqli_query($conn, $query);

    if (!$query_run) {
        return getErrorStatement(500, 'Internal Server Error : '.mysqli_error($conn));
    } 

    if (mysqli_num_rows($query_run) > 0) {
        $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
        $data = [
            'status' => 200,
            'message' => 'Customer List Fetched Successfully',
            'data' => $res
        ];
        return json_encode($data);
    } else {
        return getErrorStatement(404, 'No Customer Found');
    }
}
function getCustomerFromId($customer_input){
    global $conn;
    $customer_id = $customer_input['id'];
    $query = "SELECT * FROM customers WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $query)) {
        return getErrorStatement(500, 'Error in Preparing Statement : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_bind_param($stmt, "s", $customer_id)){
        return getErrorStatement(500, 'Error in Binding Parameters : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_execute($stmt)) {
        return getErrorStatement(500, 'Error in executing Prepared Statement : '.mysqli_error($conn));
    }

    $result = mysqli_stmt_get_result($stmt);
    if (!$result || mysqli_num_rows($result) == 0) {
        return getErrorStatement(404, 'No Customer Found');
    }

    $res = mysqli_fetch_assoc($result);
    return json_encode([
        'status' => 200,
        'message' => 'Customer List Fetched Successfully',
        'data' => $res
    ]);
}

function storeCustomer($customer_input)
{
    global $conn;

    $name = $customer_input['name'];
    $email = $customer_input['email'];
    $phone = $customer_input['phone'];

    $query = "INSERT INTO customers (name,email,phone) VALUES (?,?,?)";

    $stmt = mysqli_stmt_init($conn);    
    if (!mysqli_stmt_prepare($stmt, $query)) {
        return getErrorStatement(500, 'Error in Preparing Statement : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_bind_param($stmt, "sss", $name, $email, $phone)){
        return getErrorStatement(500, 'Error in Binding Parameters : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_execute($stmt)) {
        return getErrorStatement(500, 'Error in executing Prepared Statement : '.mysqli_error($conn));
    } else {
        return getErrorStatement(200, 'Customer Created Successfully');
    }
}

function updateCustomer($customer_input)
{
    global $conn;

    $customer_id = $customer_input['id'];
    $name = $customer_input['name'];
    $email = $customer_input['email'];
    $phone = $customer_input['phone'];

    $query = "UPDATE customers SET name = ?, email = ?, phone = ? WHERE id=?";

    $stmt = mysqli_stmt_init($conn);    
    if (!mysqli_stmt_prepare($stmt, $query)) {
        return getErrorStatement(500, 'Error in Preparing Statement : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_bind_param($stmt,  "ssss", $name, $email, $phone, $customer_id)){
        return getErrorStatement(500, 'Error in Binding Parameters : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_execute($stmt)) {
        return getErrorStatement(500, 'Error in executing Prepared Statement : '.mysqli_error($conn));
    } else {
        return getErrorStatement(200, 'Customer Updated Successfully');
    }
}

function deleteCustomer($customer_input)
{
    global $conn;
    $customer_id = $customer_input['id'];
    $query = "DELETE FROM customers WHERE id=?";

    $stmt = mysqli_stmt_init($conn);    
    if (!mysqli_stmt_prepare($stmt, $query)) {
        return getErrorStatement(500, 'Error in Preparing Statement : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_bind_param($stmt, "s", $customer_id)){
        return getErrorStatement(500, 'Error in Binding Parameters : '.mysqli_error($conn));
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        return getErrorStatement(500, 'Error in executing Prepared Statement : '.mysqli_error($conn));
    } else {
        return getErrorStatement(200, 'Customer Deleted Successfully');
    }
}
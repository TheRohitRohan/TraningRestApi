<?php
$conn = mysqli_connect("localhost", "root", "", "demo");
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

function getErrorStatement($status, $message) {
    $data = [
        'status' => $status,
        'message' => $message,
    ];
    echo json_encode($data);
    exit();
}

$input_data = json_decode(file_get_contents("php://input"), true);

if(isset($input_data['id'])){
    $id = $input_data['id'];
    $stmt = mysqli_stmt_init($conn);
    $query = "SELECT * FROM `students` WHERE stu_id=?";

    if (!mysqli_stmt_prepare($stmt, $query)) {
        getErrorStatement(500, 'Error in Preparing Statement : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_bind_param($stmt, "s", $id)){
        getErrorStatement(500, 'Error in Binding Parameters : '.mysqli_error($conn));
    }

    if (!mysqli_stmt_execute($stmt)) {
        getErrorStatement(500, 'Error in executing Prepared Statement : '.mysqli_error($conn));
    }
    $query_run = mysqli_stmt_get_result($stmt);
    
    $request_method = $_SERVER["REQUEST_METHOD"];

    if ($query_run) {
        if($request_method == "GET") {
            if (mysqli_num_rows($query_run) > 0) {
                $res = mysqli_fetch_assoc($query_run);
                $null_columns = array();
                foreach ($res as $column => $value) {
                    if (empty($value) || $value == 0) {
                        $null_columns[] = $column;
                    }
                }
                if(empty($null_columns)){
                    getErrorStatement(500, 'No null or empty values found to update');
                }
                else{
                    $data = [
                        'message' => 'Missing Fields',
                        'data' => $null_columns
                    ];
                    echo json_encode($data);
                }
            } 
            else {
                getErrorStatement(404, 'No data Found');
            }
        }
        elseif($request_method == "PUT") {
            if (mysqli_num_rows($query_run) > 0) {
                $res = mysqli_fetch_assoc($query_run);
                $update_columns = array();
                foreach ($res as $column => $value) {
                    if ((empty($value) || $value == 0) && isset($input_data[$column])) {
                        $update_columns[] = "$column = '$input_data[$column]'";
                    }
                }
                if (!empty($update_columns)) {
                    $update_query = "UPDATE `students` SET " . implode(",", $update_columns) . " WHERE stu_id=$id";
                    $update_result = mysqli_query($conn, $update_query);
                    if ($update_result) {
                        getErrorStatement(200, 'Data Updated Successfully');
                    } else {
                        getErrorStatement(500, 'Internal Server Error');
                    }
                } else {
                    getErrorStatement(400, 'No null or empty values found to update');
                }
            } 
            else {
                getErrorStatement(404, 'No data Found');
            }
        }
        else{
            getErrorStatement(405, $request_method .'Method Not Allowed');
        }
    }
    else{
        getErrorStatement(500, 'error in query execution');
    }
}
else{
    getErrorStatement(400, 'Enter Student Id');
}
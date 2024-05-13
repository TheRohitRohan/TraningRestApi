<?php
//CROSS JOIN: Fetch the Cartesian product of students and courses (every student with every course)
$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method == "GET") {
    $conn = mysqli_connect("localhost", "root", "", "demo");
    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM students CROSS JOIN courses;";

    $query_run = mysqli_query($conn, $query);
    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            foreach ($res as &$row) {
                foreach ($row as $key => &$value) {
                    if ($value === null) {
                        $value = '';
                    }
                }
            }
            $data = [
                'message' => 'CROSS JOIN',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            $output_data = json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No Data Found',
            ];
            header("HTTP/1.0 404 No Data Found");
            $output_data = json_encode($data);
        }
    } else {
        $data = [
            'status' => 405,
            'message' => $request_method . ' Method Not Allowed',
        ];
        header("HTTP/1.0 405 Method Not Allowed");
        $output_data = json_encode($data);
    }

    echo $output_data;
}
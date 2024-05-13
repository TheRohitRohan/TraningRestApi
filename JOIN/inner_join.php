<?php
//INNER JOIN: Fetch Student Details with Enrolled Courses and Grades
$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method == "GET") {
    $conn = mysqli_connect("localhost", "root", "", "demo");
    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }

    $query = "SELECT students.stu_id, students.stu_name, students.stu_age, courses.course_id, courses.course_name 
            FROM students 
            INNER JOIN courses ON students.stu_id = courses.stu_id";

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
                'message' => 'INNER JOIN',
                'data' => $res,
                'query' => $query
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
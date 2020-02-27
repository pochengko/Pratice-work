<?php
header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") { //如果是 POST 請求
    $name = $_POST['name'];
    $content = $_POST['content'];
    if ($name != null && $content != null) {
        $sql = "INSERT INTO guestbook (name, content) VALUES ('$name','$content')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(array(
                'name' => $name,
                'content' => $content
            ));
        } 
        else {
            echo json_encode(array("statusCode"=>201));
        }
    } else {
        //回傳 errorMsg json 資料
        echo json_encode(array(
            'errorMsg' => '資料未輸入完全！'
        ));
    }
} else {
    //回傳 errorMsg json 資料
    echo json_encode(array(
        'errorMsg' => '請求無效，只允許 POST 方式訪問！'
    ));
}
mysqli_close($conn);
?>
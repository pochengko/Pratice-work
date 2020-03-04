<?php
header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
require_once "bootstrap.php";
use Doctrine\DBAL\DriverManager;

$conn = DriverManager::getConnection($conn2, $config);
if ($_SERVER['REQUEST_METHOD'] == "POST") { //如果是 POST 請求
    $name = $_POST['name'];
    $content = $_POST['content'];
    $date = new DateTime("now");
    $created = $date->format('Y-m-d H:i:s');
    if ($name != null && $content != null) {
      $conn->insert('messages', array('name' => $name, 'content' => $content, 'created' => $created));
      echo json_encode(array(
        'name' => $name,
        'content' => $content
      ));
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
?>

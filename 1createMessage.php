<?php

header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //如果是 POST 請求
    $name = $_POST['name'];
    $content = $_POST['content'];
    $created = new DateTime('now');

    if ($name != null && $content != null) {
        $message = new Message();
        $message->setName($name);
        $message->setContent($content);
        $message->setCreated($created);
        $em->persist($message);
        $em->flush();

        if ($em) {
            echo json_encode([
                'name' => $name,
                'content' => $content
            ]);
        } else {
            echo json_encode(['statusCode'=>201]);
        }
    } else {
        //回傳 errorMsg json 資料
        echo json_encode([
            'errorMsg' => '資料未輸入完全！'
        ]);
    }
} else {
    //回傳 errorMsg json 資料
    echo json_encode([
        'errorMsg' => '請求無效，只允許 POST 方式訪問！'
    ]);
}
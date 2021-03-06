<?php

header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //如果是 POST 請求
    $message_id = $_POST['message_id'];
    $name = $_POST['name'];
    $content = $_POST['content'];
    $created = new DateTime('now');

    if ($name != null && $content != null) {
        $reply = new Reply();
        $reply->setName($name);
        $reply->setContent($content);
        $reply->setCreated($created);
        $id = $em->find('Message', $message_id);
        $reply->setMessage($id);

        $em->persist($reply);
        $em->flush();

        if ($em) {
            echo json_encode([
                'message_id' => $message_id,
                'name' => $name,
                'content' => $content
            ]);
        } else {
            echo json_encode(['statusCode'=>201]);
        }
    } else {
        //回傳 errorMsg json 資料
        echo json_encode([
            'message_id' => $message_id,
            'errorMsg' => '資料未輸入完全！'
        ]);
    }
} else {
    //回傳 errorMsg json 資料
    echo json_encode([
        'errorMsg' => '請求無效，只允許 POST 方式訪問！'
    ]);
}
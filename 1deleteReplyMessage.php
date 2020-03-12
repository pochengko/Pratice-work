<?php

header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
require_once 'bootstrap.php';

$id = $_GET['reply_id'];
$reply = $em->find('Reply', $id);

if ($reply === null) {
    echo "Reply $id does not exist.";
    exit(1);
} else {
    $em->remove($reply);
    $em->flush();
}
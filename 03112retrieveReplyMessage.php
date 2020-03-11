<?php

require_once 'bootstrap.php';

$dql = 'SELECT r, m FROM Reply r JOIN r.message m ORDER BY r.created DESC';

$query = $em->createQuery($dql);
$replys = $query->getResult();

foreach ($replys as $reply) {
    $id = $reply->getId();
    $message = $reply->getMessage()->getId();
    $name = $reply->getName();
    $content = $reply->getContent();
    $created = $reply->getCreated()->format('Y-m-d H:i:s');
    $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created, 'message_id'=>$message];
}

echo json_encode($res);
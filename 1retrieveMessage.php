<?php

require_once 'bootstrap.php';

$messageRepository = $em->getRepository('Message');
$messages = $messageRepository->findBy([], ['id'=>'DESC']);

foreach ($messages as $message) {
    $id = $message->getId();
    $name = $message->getName();
    $content = $message->getContent();
    $created = $message->getCreated()->format('Y-m-d H:i:s');
    $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created];
}

echo json_encode($res);
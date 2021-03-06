<?php

require_once "bootstrap.php";

// $messages = $conn->fetchAll('SELECT * FROM messages ORDER BY id DESC');

$messageRepository = $em->getRepository('Message');
$messages = $messageRepository->findBy([], ['id'=>'DESC']);

foreach ($messages as $message) {
    $id = $message->getId();
    $name = $message->getName();
    $content = $message->getContent();
    $created = $message->getCreated()->format('Y-m-d H:i:s');

    $dql = "SELECT r, m FROM Reply r JOIN r.message m WHERE r.message_id = $id ORDER BY r.created DESC";

    $query = $r_em->createQuery($dql);
    $replys = $query->getResult();

    foreach ($replys as $reply) {
        $r_id = $reply->getId();
        $r_message = $reply->getMessage()->getId();
        $r_name = $reply->getName();
        $r_content = $reply->getContent();
        $r_created = $reply->getCreated()->format('Y-m-d H:i:s');
        $r_res[] = ['id'=>"$r_id", 'name'=>$r_name, 'content'=>$r_content, 'created'=>$r_created, 'message_id'=>$r_message];
    }

    $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created. 'reply'=>$r_res];
}

echo json_encode($res);


require_once 'bootstrap.php';

$dql = 'SELECT r, m FROM Reply r JOIN r.message m ORDER BY m.id DESC, r.id DESC';

$query = $em->createQuery($dql);
$replys = $query->getResult();

foreach ($replys as $reply) {            //message
    $id = $reply->getId();
    $name = $reply->getName();
    $content = $reply->getContent();
    $created = $reply->getCreated()->format('Y-m-d H:i:s');
    $m_id = $reply->getMessage()->getId();
    $m_name = $reply->getMessage()->getName();
    $m_content = $reply->getMessage()->getContent();
    $m_created = $reply->getMessage()->getCreated()->format('Y-m-d H:i:s');
    $res[] = ['message_id'=>$m_id, 'm_name'=>$m_name, 'm_content'=>$m_content, 'm_created'=>$m_created, 'id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created];


}

echo json_encode($res);
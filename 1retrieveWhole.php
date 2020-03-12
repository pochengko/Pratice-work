<?php

require_once 'bootstrap.php';

$dql = 'SELECT r, m FROM Reply r JOIN r.message m ORDER BY m.id DESC, r.id DESC';

$query = $em->createQuery($dql);
$replys = $query->getResult();

foreach ($replys as $reply) {
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
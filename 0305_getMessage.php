<?php
require_once "bootstrap.php";
use Doctrine\DBAL\DriverManager;

$conn = DriverManager::getConnection($conn2, $config);

$users = $conn->fetchAll('SELECT * FROM messages ORDER BY id DESC');
$res = [];
foreach ($users as $user) {
  $res[] = $user;
}
echo json_encode($res);

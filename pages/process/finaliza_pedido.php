<?php

use App\Lib\DbConnection;

session_start();

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../base_url.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$conn = DbConnection::getConn();

$query = "UPDATE pedido SET finalizado = 1 WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();
header('Location:' . $base_url . '?page=admin_home&msg=2');

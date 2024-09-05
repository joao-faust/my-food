<?php

session_start();

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../base_url.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use App\Lib\DbConnection;

$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
$whats = filter_input(INPUT_POST, 'whats', FILTER_SANITIZE_SPECIAL_CHARS);
$formaDePagamentoId = filter_input(INPUT_POST, 'formaPagamento', FILTER_VALIDATE_INT);

if ($_POST['pedido']) {
  $pedidoJson = htmlspecialchars_decode($_POST['pedido'], ENT_QUOTES);
  $pedidoArray = json_decode($pedidoJson, true);

  $conn = DbConnection::getConn();

  // Cadastra pedido
  $pedidoQuery = "INSERT INTO pedido(nome, endereco, whatsApp, forma_pagamento_id)
  VALUES (?, ?, ?, ?)";

  $stmt = $conn->prepare($pedidoQuery);
  $stmt->bindParam(1, $nome);
  $stmt->bindParam(2, $endereco);
  $stmt->bindParam(3, $whats);
  $stmt->bindParam(4, $formaDePagamentoId);
  $stmt->execute();
  $pedidoId = $conn->lastInsertId();

  // Cadastra alimento no pedido e adicionais no alimento
  foreach ($pedidoArray as $pedido) {
    $alimentoId = $pedido['alimentoId'];
    $adicionais = $pedido['adicionais'];
    if (!filter_var($alimentoId, FILTER_VALIDATE_INT)) {
      header('Location:' . $base_url . '?page=inicio&msg=2');
      exit;
    }
    if ($adicionais && count($adicionais) > 0) {
      if (!filter_var_array($adicionais, FILTER_VALIDATE_INT)) {
        header('Location:' . $base_url . '?page=inicio&msg=2');
        exit;
      }
    }

    // Alimento
    $queryAlimentoPedido = "INSERT INTO alimento_pedido (alimento_id, pedido_id)
    VALUES(?, ?)";

    $stmt = $conn->prepare($queryAlimentoPedido);
    $stmt->bindParam(1, $alimentoId);
    $stmt->bindParam(2, $pedidoId);
    $stmt->execute();
    $alimentoPedidoId = $conn->lastInsertId();

    // Adicionais
    foreach ($adicionais as $adicional) {
      $queryAdicional = "INSERT INTO alimento_pedido_adicional (alimento_pedido_id,
      adicional_id) VALUES (?, ?)";

      $stmt = $conn->prepare($queryAdicional);
      $stmt->bindParam(1, $alimentoPedidoId);
      $stmt->bindParam(2, $adicional["adicionalId"]);
      $stmt->execute();
    }
  }

  header('Location:' . $base_url . '?page=inicio&msg=1');
}

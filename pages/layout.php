<?php

use App\Lib\DbConnection;

require(__DIR__ . '/base_url.php');

$conn = DbConnection::getConn();
$smtp = $conn->prepare('SELECT * FROM categoria ORDER BY nome');
$smtp->execute();
$categorias = $smtp->fetchAll();

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);

if ($page === "layout") {
  header("Location: /?page=cardapio");
  exit;
}

if ($page === "admin_layout") {
  header("Location: /?page=admin_home");
  exit;
}

$pageParts = explode("_", $page);
$admin = count($pageParts) === 2 && $pageParts[0] === "admin";

if ($admin && isset($pageParts[1]) && $pageParts[1] !== "login") {
  if (!isset($_SESSION["loggedIn"])) {
    header("Location: /?page=admin_login");
    exit;
  }
}

$headerTitle = match ($page) {
  "inicio" => "Cardápio",
  "adicionais" => "Adicionais",
  default => "My Food",
};

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <?php if ($admin): ?>
    <!-- My css -->
    <link rel="stylesheet" href="/public/css/admin.css">
  <?php else: ?>
    <!-- My css -->
    <link rel="stylesheet" href="<?= $base_url ?>public/css/styles.css">
  <?php endif; ?>
  <!-- Bootstrap js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
  <title>My food</title>
</head>

<body>
  <?php if (!$admin): ?>
    <header>
      <div class="cardapio-banner">
        <h3><?= $headerTitle ?></h3>
      </div>
      <nav>
        <ul>
          <?php if ($page === "inicio"): ?>
            <?php foreach ($categorias as $categoria): ?>
              <li>
                <a href="#categoria_<?= $categoria['id'] ?>">
                  <?= $categoria['nome'] ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
  <?php endif; ?>

  <div class="main-container container mt-4">
    <?php
    $msg = filter_input(INPUT_GET, 'msg', FILTER_VALIDATE_INT);
    ?>
    <?php
    if ($msg) {
      if ($msg == 1) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Seu pedido foi finalizado
          <button type="button" class="btn-close" data-bs-dismiss="alert"
          aria-label="Close"></button>
        </div>
      <?php } else if ($msg == 2) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Pedido finalizado com sucesso
          <button type="button" class="btn-close" data-bs-dismiss="alert"
          aria-label="Close"></button>
        </div>
      <?php }
    }
    ?>
    <?php
    $filePath = __DIR__ . '/' . $page . '.php';
    if (file_exists($filePath)) {
      if ($admin) {
        require(__DIR__ . '/admin_layout.php');
      } else {
        require($filePath);
      }
    } else {
      require('pages/not_found.php');
    }
    ?>
  </div>

  <br>

  <?php if (!$admin): ?>
    <nav class="navbar">
      <a href="?page=inicio" class="active">
        <span class="icon">&#8962;</span>
        Início
      </a>
      <a href="?page=carrinho">
        <span class="icon">&#128722;</span>
        Carrinho
      </a>
    </nav>
  <?php endif; ?>

</body>

</html>

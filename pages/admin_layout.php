<?php

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);
require __DIR__ . '/base_url.php';

?>

<div class="sidebar">
  <h1>MY FOOD</h1>
  <a class="<?= $page === "admin_home" ? "selected" : "" ?>"
  href="<?= $base_url ?>?page=admin_home">
    Inicio
  </a>
</div>

<div class="main-content">
  <?php
  $msg = filter_input(INPUT_GET, 'msg', FILTER_VALIDATE_INT);
  ?>
  <?php
  $filePath = __DIR__ . '/' . $page . '.php';
  if (file_exists($filePath)) {
    require($filePath);
  } else {
    require('pages/not_found.php');
  }
  ?>
</div>

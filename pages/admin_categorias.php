<?php

use App\Lib\DbConnection;

$conn = DbConnection::getConn();
$smtp = $conn->prepare('SELECT * FROM categoria ORDER BY nome');
$smtp->execute();
$categorias = $smtp->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="text-center">Categorias</h1>
<div class="header-buttons">
</div>

<div class="food-list mt-4">
  <h2 class="mb-4">Categorias</h2>
  <?php foreach ($categorias as $categoria): ?>
    <div class="food-item">
      <label><?php echo htmlspecialchars($categoria['nome']); ?></label>
      <div class="toggle-switch">
        <label class="switch">
          <input type="checkbox" id="switch<?php echo $categoria['id']; ?>" checked>
          <span class="slider round"></span>
        </label>
        <span>Ativado</span>
      </div>
    </div>
  <?php endforeach; ?>
</div>
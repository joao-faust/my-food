<?php

use App\Lib\DbConnection;

$conn = DbConnection::getConn();
$query = "SELECT
    categoria.id AS categoria_id,
    categoria.nome AS categoria_nome,
    alimento.id AS alimento_id,
    alimento.nome AS alimento_nome,
    alimento.descricao,
    alimento.preco,
    alimento.foto
  FROM alimento
  JOIN categoria ON alimento.categoria_id = categoria.id
  ORDER BY categoria.nome, alimento.nome";

$smtp = $conn->prepare($query);
$smtp->execute();
$alimentos = $smtp->fetchAll(PDO::FETCH_ASSOC);

$categorias = [];

foreach ($alimentos as $alimento) {
  $categoriaId = $alimento['categoria_id'];
  $categorias[$categoriaId]["nome"] = $alimento['categoria_nome'];
  $categorias[$categoriaId]['alimentos'][] = $alimento;
}
?>

<?php foreach ($categorias as $categoriaId => $categoria): ?>

  <h5 id="categoria_<?= $categoriaId ?>">
    <?= $categoria['nome'] ?>
  </h5>
  <div class="categoria-alimentos">
    <?php foreach ($categoria['alimentos'] as $alimento): ?>
      <a href="?page=adicionais&alimento_id=<?= $alimento['alimento_id'] ?>">
        <div class="alimento mb-3">
          <div class="alimento-corpo">
            <h6 class="m-0">
              <?= $alimento['alimento_nome'] ?>
            </h6>
            <p class="m-0">
              <?= $alimento['descricao'] ?>
            </p>
            <span class="preco-alimento text-danger">
              <?= $alimento['preco'] ?>
            </span>
          </div>
          <div class="alimento-cabecalho">

            <img src="<?= $alimento['foto'] ?>" alt="foto alimento" class="foto-alimento">
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>
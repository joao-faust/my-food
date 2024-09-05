<?php
use App\Lib\DbConnection;

$conn = DbConnection::getConn();

$query = "SELECT p.*, fp.nome AS forma_pagamento FROM pedido p
INNER JOIN forma_de_pagamento fp ON p.forma_pagamento_id = fp.id
WHERE p.finalizado = 0";

$stmt = $conn->prepare($query);
$stmt->execute();

$pedidos = $stmt->fetchAll();
?>

<h1>Pedidos de hoje</h1>

<div class="pedidos">
  <?php
  $alimentoPedidoQuery = "SELECT ap.id AS alimento_pedido_id, a.nome AS nome_alimento,
  a.preco AS alimento_preco FROM alimento_pedido ap INNER JOIN alimento a
  ON ap.alimento_id = a.id WHERE pedido_id = ?";
  if ($pedidos) {
    foreach ($pedidos as $pedido) {
      $stmt = $conn->prepare($alimentoPedidoQuery);
      $stmt->bindParam(1, $pedido['id']);
      $stmt->execute();
      $alimentos = $stmt->fetchAll();
      $precoTotal = 0.0;

      ?>
      <div class="pedido-card">
        <form action="<?= $base_url ?>pages/process/finaliza_pedido.php" method="POST">
          <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
          <button class="mb-2 btn btn-danger" type="submit">
            Finalizar
          </button>
        </form>

        <h5>Informações do cliente</h5>
        <div>
          Nome:
          <?= $pedido['nome'] ?>
        </div>
        <div>
          WhatsApp:
          <?= $pedido['whatsApp'] ?>
        </div>
        <div>Forma de pagamento: <?= $pedido['forma_pagamento'] ?></div>
        <div>
          Endereco:
          <?= $pedido['endereco'] ?>
        </div>
        <div>
          Data do pedido:
          <?= date('d/m/Y H:i:s', strtotime($pedido['criado_em'])) ?>
        </div>

        <h5 class="mt-2">
          Informações do pedido
        </h5>
        <div>
          <ul>
          <?php
          $adicionaisQuery = "SELECT a.nome, a.preco FROM adicional a INNER JOIN
          alimento_pedido_adicional apa ON apa.adicional_id = a.id
          WHERE apa.alimento_pedido_id = ?";
          foreach ($alimentos as $alimento) {
            $stmt = $conn->prepare($adicionaisQuery);
            $stmt->bindParam(1, $alimento['alimento_pedido_id']);
            $stmt->execute();
            $adicionais = $stmt->fetchAll();
            $precoTotal += $alimento['alimento_preco'];
            ?>
            <li>
              <?= $alimento['nome_alimento'] ?>
              <ul class="m-0">
                <?php
                if ($adicionais) {
                  foreach ($adicionais as $adicional) {
                    $precoTotal += $adicional['preco'];
                    ?>
                    <li><?= $adicional['nome'] ?></li>
                  <?php }
                }
                ?>
              </ul>
            </li>
          <?php }
          ?>
          </ul>
        </div>
        <div>
          Preco total:
          <?= $precoTotal ?> R$
        </div>
      </div>
      <br>
    <?php }
  }
  ?>
</div>

<script>
  setInterval(() => {
    window.location.reload();
  }, 120000); // 2 mintuos
</script>

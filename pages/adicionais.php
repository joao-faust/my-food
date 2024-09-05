<a href="?page=inicio" class="botao-cardapio" title="Fechar">X</a>

<div class="alimento-container">
  <?php

  use App\Lib\DbConnection;

  $conn = DbConnection::getConn();

  $alimentoId = filter_input(INPUT_GET, 'alimento_id', FILTER_VALIDATE_INT);

  if (!$alimentoId) {
    exit;
  }

  $alimentoQuery = 'SELECT * FROM alimento WHERE id = ?';
  $stmt = $conn->prepare($alimentoQuery);
  $stmt->bindParam(1, $alimentoId);
  $stmt->execute();
  $alimento = $stmt->fetch();

  $alimentoNome = $alimento["nome"];

  if ($alimento) { ?>
    <img src='<?= $alimento['foto'] ?>' alt='Imagem do alimento'>
    <div class='alimento-nome'>
      <?= $alimento['nome'] ?>
    </div>
    <div class='alimento-preco text-center' style="margin: 0;"
      data-preco="<?= $alimento['preco'] ?>">
      R$ <?= $alimento['preco'] ?>
    </div>
  <?php }

  $categoriasQuery = 'SELECT
      ad.nome AS adicional_nome,
      ad.preco AS adicional_preco,
      ad.descricao AS adicional_descricao,
      ad.id AS adicional_id
    FROM alimento a
    INNER JOIN categoria c ON a.categoria_id = c.id
    INNER JOIN adicional_categoria ac ON ac.categoria_id = c.id
    INNER JOIN adicional ad ON ac.adicional_id = ad.id
    WHERE a.id = :alimento_id';

  $stmt = $conn->prepare($categoriasQuery);
  $stmt->bindParam(':alimento_id', $alimentoId);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($result && $alimento = $result[0]): ?>
    <!-- adicionais -->
    <?php foreach ($result as $adicional): ?>
      <div
        class='adicional-container'
        data-adicional-id="<?= $adicional['adicional_id'] ?>"
        data-adicional-nome="<?= $adicional['adicional_nome'] ?>">
        <div class='adicional-info'>
          <div class='adicional-nome'>
            <?= $adicional['adicional_nome'] ?>
          </div>
          <div class="adicional-preco" data-preco="<?= $adicional['adicional_preco'] ?>">
            <?= $adicional['adicional_preco'] ?>
          </div>
          <div class="adicional-descricao">
            <?= $adicional['adicional_descricao'] ?>
          </div>
        </div>
        <div class='adicional-quantidade'>
          <button class="toogle-adicional" data-value="0">
            Não
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div>
      <p>Não há adicionais para este alimento</p>
    </div>
  <?php endif; ?>
</div>

<div class="d-flex justify-content-center" style="max-width: 250px; margin: auto;">
  <a href="#" class="botao-confirmar" data-alimentonome="<?= $alimentoNome ?>" data-alimentoid="<?= $alimentoId ?>"
    id="botaoConfirmar">
    Confirmar
  </a>
</div>

<script src="<?= $base_url ?>/public/js/adicionais.js"></script>
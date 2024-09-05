<h5>Carrinho</h5>

<hr>

<!-- formlário -->

<h6>Preencha o formulário com seus dados</h6>

<form action="<?= $base_url ?>pages/process/faz_pedido.php" method="POST"
  id="pedidoForm" autocomplete="off">
  <div class="form-group">
    <label for="nome">Seu nome</label>
    <input type="text" name="nome" id="nome" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="endereco">Seu endereço</label>
    <textarea name="endereco" id="endereco" class="form-control" required></textarea>
  </div>
  <div class="form-group">
    <label for="whats">Seu WhatsApp</label>
    <input type="number" name="whats" id="whats" class="form-control" required>
  </div>

  <!-- formas de pagamento -->

  <h6>Escolha a forma de pagamento</h6>

  <?php

  use App\Lib\DbConnection;

  $conn = DbConnection::getConn();
  $stmt = $conn->prepare('SELECT * FROM forma_de_pagamento');
  $stmt->execute();
  $formasDePagamento = $stmt->fetchAll();
  ?>

  <select name="formaPagamento" id="formaPagamento" class="form-select" required>
    <?php if ($formasDePagamento && count($formasDePagamento) > 0): ?>
      <?php foreach ($formasDePagamento as $formaDePagamento): ?>
        <option value="<?= $formaDePagamento['id'] ?>">
          <?= $formaDePagamento['nome'] ?>
        </option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>

  <hr class="mt-4">

  <div class="btns-carrinho">
    <div>
      <div>
        <button class="botao" type="button" data-bs-toggle="modal"
          data-bs-target="#staticBackdrop">
          Revisar lista de alimentos
        </button>
      </div>
      <div class="mt-3">
        <button class="botao" type="button" id="addMaisPratos">
          Adicionar mais pratos
        </button>
      </div>
      <div class="mt-4">
        <button class="botao" type="submit" id="finalizarPedidoBtn">
          Finalizar pedido
        </button>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Lista de alimentos</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="listaAlimentosModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById("nome").addEventListener("blur", () => {
    localStorage.setItem('nome', document.getElementById("nome").value);
  })

  document.getElementById("endereco").addEventListener("blur", () => {
    localStorage.setItem('endereco', document.getElementById("endereco").value);
  })

  document.getElementById("whats").addEventListener("blur", () => {
    localStorage.setItem('whats', document.getElementById("whats").value);
  })

  document.getElementById("formaPagamento").addEventListener("change", () => {
    localStorage.setItem('formaPagamento', document.getElementById("formaPagamento").value);
  })

  const InputNome = document.getElementById("nome");
  const InputEndereco = document.getElementById("endereco");
  const InputWhats = document.getElementById("whats");
  const InputFormaPagamento = document.getElementById("formaPagamento");

  InputNome.value = localStorage.getItem('nome');
  InputEndereco.value = localStorage.getItem('endereco');
  InputWhats.value = localStorage.getItem('whats');
  InputFormaPagamento.value = localStorage.getItem('formaPagamento');

  document.getElementById('addMaisPratos')
    .addEventListener('click', () => {
      window.location.href = '<?= $base_url ?>?page=inicio';
    });

  document.getElementById('pedidoForm')
    .addEventListener('submit', (e) => {
      if (!localStorage.getItem('pedido')) {
        alert('Nenhum produto foi adicionado');
        e.preventDefault();
        return;
      }
      const pedido = localStorage.getItem('pedido');
      const pedidoForm = document.getElementById('pedidoForm');
      const pedidoEscaped = pedido.replace(/"/g, '&quot;');
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'pedido';
      input.value = pedidoEscaped;
      pedidoForm.appendChild(input);
      localStorage.removeItem('pedido');
    });

  const myModal = document.getElementById('staticBackdrop');

  myModal.addEventListener('show.bs.modal', () => {
    const modalBody = document.getElementById('listaAlimentosModalBody');
    modalBody.innerHTML = ''; // Limpa o conteúdo atual do modal

    const pedido = localStorage.getItem('pedido');
    if (pedido) {
      const pedidoList = JSON.parse(pedido);
      if (pedidoList.length === 0) {
        modalBody.innerHTML = '<p>Seu pedido está vazio.</p>';
        return;
      }

      const ul = document.createElement('ul');
      let precoTotal = 0;

      pedidoList.forEach((item, index) => {
        const li = document.createElement('li');
        // Gera a lista de adicionais com preço
        const adicionaisList = item.adicionais.length > 0 ?
          item.adicionais.map(adicional =>
            `<li>${adicional.adicionalNome} (R$ ${adicional.preco.toFixed(2)})</li>`
          ).join('') :
          '<li>Nenhum</li>';
        // Calcula o preço total dos adicionais
        const precoItem = parseFloat(item.preco);
        const precoAdicionais = item.adicionais.reduce((total, adicional) => total + parseFloat(adicional.preco), 0);
        precoTotal += precoItem + precoAdicionais;

        li.innerHTML = `
        <div><strong>Alimento:</strong> ${item.alimentoNome}</div>
        <div><strong>Preço:</strong> R$ ${precoItem.toFixed(2)}</div>
        <div><strong>Adicionais:</strong></div>
        <ul>${adicionaisList}</ul>
        <button class="btn btn-danger btn-sm mt-2" data-item-index="${index}">Remover</button>
        <hr>
      `;
        ul.appendChild(li);
      });

      const totalDiv = document.createElement('div');
      totalDiv.innerHTML = `<strong>Preço Total:</strong> R$ ${precoTotal.toFixed(2)}`;
      modalBody.appendChild(ul);
      modalBody.appendChild(totalDiv);
    } else {
      modalBody.innerHTML = '<p>Seu pedido está vazio.</p>';
    }
  });

  // Adiciona o evento de clique para os botões de remoção
  document.getElementById('staticBackdrop').addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-danger')) {
      const itemIndex = event.target.dataset.itemIndex;
      removeItemFromPedido(itemIndex);
    }
  });

  function removeItemFromPedido(itemIndex) {
    let pedido = JSON.parse(localStorage.getItem('pedido'));
    if (pedido) {
      // Filtra o pedido removendo o item com o id correspondente
      // console.log(pedido);
      pedido = pedido.filter((item, index) => Number(itemIndex) !== index);
      // console.log(pedido);

      if (pedido.length === 0) {
        localStorage.removeItem('pedido');
      } else {
        localStorage.setItem('pedido', JSON.stringify(pedido));
      }
      // Atualiza o modal para refletir as mudanças
      const modalBody = document.getElementById('listaAlimentosModalBody');
      modalBody.innerHTML = '';
      if (pedido.length === 0) {
        modalBody.innerHTML = '<p>Seu pedido está vazio.</p>';
      } else {
        myModal.dispatchEvent(new Event('show.bs.modal')); // Recarrega o modal
      }
    }
  }
</script>

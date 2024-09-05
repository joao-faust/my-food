document.addEventListener('DOMContentLoaded', () => {
  const adicionais = document.querySelectorAll('.adicional-container');
  let precoTotal = parseFloat(document.querySelector('.alimento-preco').dataset.preco);
  let adicionaisLista = [];

  adicionais.forEach(adicional => {
    const adicionalId = adicional.dataset.adicionalId;
    const adicionalNome = adicional.dataset.adicionalNome;
    const toogleAdicional = adicional.querySelector('.toogle-adicional');
    const precoElemento = adicional.querySelector('.adicional-preco');

    const precoAdicional = parseFloat(precoElemento.dataset.preco);

    const somaPrecoTotal = (valor) => {
      precoTotal += valor;
      document.querySelector('.alimento-preco').textContent = `R$ ${precoTotal.toFixed(2)}`;
    };

    const subtraiPrecoTotal = (valor) => {
      precoTotal -= valor;
      document.querySelector('.alimento-preco').textContent = `R$ ${precoTotal.toFixed(2)}`;
    }

    const atualizarObjetoAdicionais = (e) => {
      const btn = e.target;
      const statusValue = btn.dataset.value;
      if (statusValue == '0') {
        btn.innerHTML = 'Sim';
        btn.dataset.value = '1';
        adicionaisLista.push({ adicionalId, adicionalNome, preco: precoAdicional });
        somaPrecoTotal(precoAdicional);
      } else {
        btn.innerHTML = 'Não';
        btn.dataset.value = '0';
        adicionaisLista = adicionaisLista.filter((valor) => valor.adicionalId != adicionalId);
        subtraiPrecoTotal(precoAdicional);
      }
    };

    toogleAdicional.addEventListener('click', atualizarObjetoAdicionais);
  });

  document.getElementById('botaoConfirmar').addEventListener('click', (e) => {
    e.preventDefault();

    const btn = e.target;
    const alimentoId = btn.dataset.alimentoid;
    const alimentoNome = btn.dataset.alimentonome;

    if (localStorage.getItem('pedido')) {
      const pedido = JSON.parse(localStorage.getItem('pedido'));
      pedido.push({
        preco: parseFloat(document.querySelector('.alimento-preco').dataset.preco),
        alimentoId,
        alimentoNome,
        adicionais: adicionaisLista
      });
      localStorage.setItem('pedido', JSON.stringify(pedido));
    } else {
      localStorage.setItem('pedido', JSON.stringify(
        [
          {
            preco: parseFloat(document.querySelector('.alimento-preco').dataset.preco),
            alimentoNome,
            alimentoId,
            adicionais: adicionaisLista
          }
        ]
      ));
    }

    window.location.href = '/?page=carrinho';
  });
});

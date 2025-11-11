async function carregarDisciplina() {
  const resposta = await fetch('index.php?acao=listar');
  const dados = await resposta.json();
  const tabela = document.getElementById('tabela');
  tabela.innerHTML = '';

  if (dados.length === 0) {
    tabela.innerHTML = '<tr><td colspan="4">Nenhuma disciplina cadastrada.</td></tr>';
    return;
  }

  dados.forEach(d => {
    tabela.innerHTML += `
      <tr>
        <td>${d.id}</td>
        <td>${d.nome}</td>
        <td>${d.carga} h</td>
        <td>
          <button onclick="editar(${d.id}, '${d.nome}', ${d.carga})">Editar</button>
          <button onclick="excluir(${d.id})">Excluir</button>
        </td>
      </tr>
    `;
  });
}

async function salvarDisciplina(event) {
  event.preventDefault();
  const form = document.getElementById('formDisciplina');
  const formData = new FormData(form);
  const id = formData.get('id');
  formData.append('acao', id ? 'editar' : 'adicionar');

  const resposta = await fetch('index.php', { method: 'POST', body: formData });
  const resultado = await resposta.json();

  if (resultado.status === 'ok') {
    form.reset();
    document.getElementById('cancelar').style.display = 'none';
    document.querySelector('button[type=submit]').textContent = 'Cadastrar';
    carregarDisciplinas();
  } else {
    alert(resultado.mensagem || 'Erro ao salvar disciplina');
  }
}

function editar(id, nome, carga) {
  document.getElementById('id').value = id;
  document.getElementById('nome').value = nome;
  document.getElementById('carga').value = carga;
  document.querySelector('button[type=submit]').textContent = 'Salvar Alterações';
  document.getElementById('cancelar').style.display = 'inline';
}

function cancelarEdicao() {
  const form = document.getElementById('formDisciplina');
  form.reset();
  document.getElementById('id').value = '';
  document.querySelector('button[type=submit]').textContent = 'Cadastrar';
  document.getElementById('cancelar').style.display = 'none';
}

async function excluir(id) {
  if (!confirm('Deseja excluir esta disciplina?')) return;
  const formData = new FormData();
  formData.append('acao', 'excluir');
  formData.append('id', id);

  const resposta = await fetch('index.php', { method: 'POST', body: formData });
  const resultado = await resposta.json();

  if (resultado.status === 'ok') {
    carregarDisciplinas();
  } else {
    alert(resultado.mensagem || 'Erro ao excluir');
  }
}

// Inicializa
document.getElementById('formDisciplina').addEventListener('submit', salvarDisciplina);
window.onload = carregarDisciplinas;
document.addEventListener('DOMContentLoaded', carregarDisciplinas);

const form = document.getElementById('formDisciplina');
const tabela = document.getElementById('tabelaDisciplinas');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const dados = {
    id: form.id.value,
    nome: form.nome.value,
    carga: form.carga.value
  };

  const resposta = await fetch('api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(dados)
  });

  const resultado = await resposta.json();
  alert(resultado.mensagem);

  // ✅ Limpa o formulário totalmente (inclusive o id)
  form.reset();
  form.id.value = ''; // <-- importante!
  carregarDisciplinas();
});

async function carregarDisciplinas() {
  const resposta = await fetch('api.php');
  const disciplinas = await resposta.json();

  tabela.innerHTML = '';
  disciplinas.forEach(d => {
    const linha = document.createElement('tr');
    linha.innerHTML = `
      <td>${d.id_disciplina}</td>
      <td>${d.nome}</td>
      <td>${d.carga_horaria}</td>
      <td>
        <button onclick="editar(${d.id_disciplina}, '${d.nome}', ${d.carga_horaria})">Editar</button>
        <button onclick="excluir(${d.id_disciplina})">Excluir</button>
      </td>
    `;
    tabela.appendChild(linha);
  });
}

function editar(id, nome, carga) {
  form.id.value = id;
  form.nome.value = nome;
  form.carga.value = carga;
}

async function excluir(id) {
  if (confirm('Deseja realmente excluir esta disciplina?')) {
    const resposta = await fetch('api.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const resultado = await resposta.json();
    alert(resultado.mensagem);

    // ✅ Limpa o formulário e o campo oculto id
    form.reset();
    form.id.value = '';
    carregarDisciplinas();
  }
}

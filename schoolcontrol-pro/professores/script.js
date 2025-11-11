const form = document.getElementById('formprof');
const lista = document.getElementById('listaprofessor');

function carregarprofessores() {
  fetch('prof.php')
    .then(res => res.json())
    .then(dados => {
      lista.innerHTML = '';
      dados.forEach(a => {
        lista.innerHTML += `
          <tr>
            <td>${a.id_professor}</td>
            <td>${a.nome}</td>
            <td>${a.email}</td>
            <td>${a.telefone ?? ''}</td>
            <td>
              <button onclick="editar(${a.id_professor}, '${a.nome}', '${a.email}', '${a.telefone ?? ''}')">Editar</button>
              <button onclick="excluir(${a.id_professor})">Excluir</button>
            </td>
          </tr>
        `;
      });
    })
    .catch(err => console.error('Erro ao carregar professores:', err));
}

form.onsubmit = e => {
  e.preventDefault();

  const idValor = document.getElementById('id').value;
  const prof = {
    nome: document.getElementById('nome').value,
    email: document.getElementById('email').value,
    telefone: document.getElementById('tel').value
  };

  if (idValor) {
    prof.id = parseInt(idValor);
  }

  fetch('prof.php', {
    method: idValor ? 'PUT' : 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(prof)
  })
    .then(res => res.json())
    .then(() => {
      form.reset();
      document.getElementById('id').value = '';
      carregarprofessores();
    })
    .catch(err => console.error('Erro ao salvar professor:', err));
};

function editar(id, nome, email, telefone) {
  document.getElementById('id').value = id;
  document.getElementById('nome').value = nome;
  document.getElementById('email').value = email;
  document.getElementById('tel').value = telefone;
}

function excluir(id) {
  if (confirm('Deseja realmente excluir este professor?')) {
    fetch('prof.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
      .then(res => res.json())
      .then(() => carregarprofessores())
      .catch(err => console.error('Erro ao excluir professor:', err));
  }
}

carregarprofessores();

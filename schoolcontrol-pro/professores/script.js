const form = document.getElementById('formprof');
const lista = document.getElementById('listaprof');

function carregarprofessores() {
    fetch('prof.php', {
        method: 'GET',
    })
    .then(res => res.json())
    .then(dados => {
        lista.innerHTML = '';
        dados.forEach(a => {
            lista.innerHTML += `
                <tr>
                    <td>${a.id}</td>
                    <td>${a.nome}</td>
                    <td>${a.disciplina}</td>
                    <td>${a.email}</td>
                    <td>${a.telefone}</td>
                    <td>
                        <button onclick="editar(${a.id}, '${a.nome}', '${a.disciplina}', '${a.email}', '${a.telefone}')">Editar</button>
                        <button onclick="excluir(${a.id})">Excluir</button>
                    </td>
                </tr>
            `;
        });
    })
    .catch(error => {
        console.error('Erro ao carregar professores:', error);
    });
}

form.onsubmit = e => {
    e.preventDefault();

    const idValor = document.getElementById('id').value;

    const prof = {
        nome: document.getElementById('nome').value,
        disciplina: document.getElementById('dis').value,
        email: document.getElementById('email').value,
        telefone: document.getElementById('tel').value
    };

    const metodo = idValor ? 'PUT' : 'POST';

    if (idValor) {
        prof.id = parseInt(idValor);
    }

    fetch('prof.php', {
        method: metodo,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(prof)
    })
    .then(res => res.json())
    .then(() => {
        form.reset();
        document.getElementById('id').value = '';
        carregarprofessores();
    })
    .catch(error => {
        console.error('Erro ao enviar os dados dos professores:', error);
    });
};

function editar(id, nome, disciplina, email, telefone) {
    document.getElementById('id').value = id;
    document.getElementById('nome').value = nome;
    document.getElementById('dis').value = disciplina;
    document.getElementById('email').value = email;
    document.getElementById('tel').value = telefone;
}

function excluir(id) {
    if (confirm('Deseja realmente excluir?')) {
        fetch('prof.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(() => carregarprofessores())
        .catch(error => {
            console.error('Erro ao excluir professor:', error);
        });
    }
}

carregarprofessores();

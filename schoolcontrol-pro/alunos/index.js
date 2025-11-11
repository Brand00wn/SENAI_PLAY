const tabela = document.querySelector("#tabelaAlunos tbody");
const form = document.getElementById("formAluno");
const btnSalvar = document.getElementById("btnSalvar");
const btnCancelar = document.getElementById("btnCancelar");

let editando = false;

// ====== Funções de armazenamento ======
function getAlunos() {
    return JSON.parse(localStorage.getItem("alunos") || "[]");
}

function salvarAlunos(alunos) {
    localStorage.setItem("alunos", JSON.stringify(alunos));
}

// ====== Listar alunos ======
function listarAlunos() {
    const alunos = getAlunos();
    tabela.innerHTML = "";

    if (alunos.length === 0) {
        tabela.innerHTML = "<tr><td colspan='5'>Nenhum aluno cadastrado.</td></tr>";
        return;
    }

    alunos.forEach(aluno => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${aluno.id}</td>
            <td>${aluno.nome}</td>
            <td>${aluno.email}</td>
            <td>${new Date(aluno.data_nascimento).toLocaleDateString('pt-BR')}</td>
            <td>
                <button onclick="editarAluno(${aluno.id})">Editar</button>
                <button onclick="excluirAluno(${aluno.id})">Excluir</button>
            </td>
        `;
        tabela.appendChild(tr);
    });
}

// ====== Cadastrar ou Editar ======
form.addEventListener("submit", function(e) {
    e.preventDefault();
    const alunos = getAlunos();

    const id = document.getElementById("id").value;
    const nome = document.getElementById("nome").value.trim();
    const email = document.getElementById("email").value.trim();
    const data_nascimento = document.getElementById("data_nascimento").value;

    if (!nome || !email || !data_nascimento) {
        alert("Preencha todos os campos!");
        return;
    }

    if (editando) {
        // Atualiza aluno existente
        const index = alunos.findIndex(a => a.id == id);
        if (index >= 0) {
            alunos[index] = { id: parseInt(id), nome, email, data_nascimento };
            salvarAlunos(alunos);
            alert("Aluno atualizado com sucesso!");
        }
        editando = false;
        btnSalvar.textContent = "Cadastrar";
        btnCancelar.style.display = "none";
    } else {
        // Novo cadastro
        const novoAluno = {
            id: alunos.length > 0 ? alunos[alunos.length - 1].id + 1 : 1,
            nome,
            email,
            data_nascimento
        };
        alunos.push(novoAluno);
        salvarAlunos(alunos);
        alert("Aluno cadastrado com sucesso!");
    }

    form.reset();
    listarAlunos();
});

// ====== Editar aluno ======
function editarAluno(id) {
    const alunos = getAlunos();
    const aluno = alunos.find(a => a.id == id);
    if (!aluno) return alert("Aluno não encontrado!");

    document.getElementById("id").value = aluno.id;
    document.getElementById("nome").value = aluno.nome;
    document.getElementById("email").value = aluno.email;
    document.getElementById("data_nascimento").value = aluno.data_nascimento;

    editando = true;
    btnSalvar.textContent = "Salvar Alterações";
    btnCancelar.style.display = "inline";
}

// ====== Cancelar edição ======
btnCancelar.addEventListener("click", () => {
    form.reset();
    editando = false;
    btnSalvar.textContent = "Cadastrar";
    btnCancelar.style.display = "none";
});

// ====== Excluir aluno ======
function excluirAluno(id) {
    if (!confirm("Deseja realmente excluir este aluno?")) return;
    let alunos = getAlunos();
    alunos = alunos.filter(a => a.id != id);
    salvarAlunos(alunos);
    listarAlunos();
    alert("Aluno excluído!");
}

// ====== Inicializar ======
listarAlunos();
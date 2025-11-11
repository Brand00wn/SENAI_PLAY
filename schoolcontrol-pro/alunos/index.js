document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = "api.php"; // caminho da sua API PHP
    const tabela = document.querySelector("#tabelaAlunos tbody");
    const form = document.getElementById("formAluno");
    const btnSalvar = document.getElementById("btnSalvar");
    const btnCancelar = document.getElementById("btnCancelar");

    let editando = false;

    // ===== Listar alunos =====
    async function listarAlunos() {
        try {
            const res = await fetch(`${apiUrl}?acao=listar`);
            const data = await res.json();

            tabela.innerHTML = "";
            data.forEach(aluno => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${aluno.id}</td>
                    <td>${aluno.nome}</td>
                    <td>${aluno.email}</td>
                    <td>${new Date(aluno.data_nascimento).toLocaleDateString('pt-BR')}</td>
                    <td>
                        <button class="btnEditar" data-id="${aluno.id}">Editar</button>
                        <button class="btnExcluir" data-id="${aluno.id}">Excluir</button>
                    </td>
                `;
                tabela.appendChild(tr);
            });

            // adiciona eventos de clique nos botões
            document.querySelectorAll(".btnEditar").forEach(btn => {
                btn.addEventListener("click", () => editarAluno(btn.dataset.id));
            });
            document.querySelectorAll(".btnExcluir").forEach(btn => {
                btn.addEventListener("click", () => excluirAluno(btn.dataset.id));
            });

        } catch (error) {
            console.error("Erro ao listar alunos:", error);
        }
    }

    // ===== Cadastrar / Editar =====
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const acao = editando ? "editar" : "cadastrar";

        try {
            const res = await fetch(`${apiUrl}?acao=${acao}`, {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            if (data.error) {
                alert(data.error);
            } else {
                alert(editando ? "Aluno atualizado!" : "Aluno cadastrado!");
                form.reset();
                editando = false;
                btnSalvar.textContent = "Cadastrar";
                btnCancelar.style.display = "none";
                listarAlunos();
            }

        } catch (error) {
            console.error("Erro ao salvar aluno:", error);
        }
    });

    // ===== Editar aluno =====
    async function editarAluno(id) {
        try {
            const res = await fetch(`${apiUrl}?acao=listar`);
            const data = await res.json();
            const aluno = data.find(a => a.id == id);

            if (!aluno) return alert("Aluno não encontrado.");

            document.getElementById("id").value = aluno.id;
            document.getElementById("nome").value = aluno.nome;
            document.getElementById("email").value = aluno.email;
            document.getElementById("data_nascimento").value = aluno.data_nascimento;

            editando = true;
            btnSalvar.textContent = "Salvar Alterações";
            btnCancelar.style.display = "inline";

        } catch (error) {
            console.error("Erro ao editar aluno:", error);
        }
    }

    // ===== Cancelar edição =====
    btnCancelar.addEventListener("click", () => {
        form.reset();
        editando = false;
        btnSalvar.textContent = "Cadastrar";
        btnCancelar.style.display = "none";
    });

    // ===== Excluir aluno =====
    async function excluirAluno(id) {
        if (!confirm("Deseja realmente excluir este aluno?")) return;

        try {
            const res = await fetch(`${apiUrl}?acao=excluir&id=${id}`);
            const data = await res.json();

            if (data.error) {
                alert(data.error);
            } else {
                alert("Aluno excluído!");
                listarAlunos();
            }

        } catch (error) {
            console.error("Erro ao excluir aluno:", error);
        }
    }

    // ===== Inicializar =====
    listarAlunos();
});

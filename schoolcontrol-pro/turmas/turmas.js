const form = document.getElementById("turmaForm");
const tabela = document.querySelector("#tabelaTurmas tbody");
const cancelarBtn = document.getElementById("cancelarBtn");
const apiBase = "."; // mesma pasta

// ======== CARREGAR LISTA ========
async function carregarTurmas() {
    const resp = await fetch(`${apiBase}/listar.php`);
    const turmas = await resp.json();
    tabela.innerHTML = "";

    if (turmas.length === 0) {
        tabela.innerHTML = "<tr><td colspan='5'>Nenhuma turma cadastrada.</td></tr>";
        return;
    }

    turmas.forEach(t => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${t.id_turma}</td>
            <td>${t.nome}</td>
            <td>${t.ano}</td>
            <td>${t.turno || "-"}</td>
            <td>
                <button onclick="editarTurma(${t.id_turma}, '${t.nome}', ${t.ano}, '${t.turno || ""}')">✏️</button>
                <button class="delete" onclick="excluirTurma(${t.id_turma})">🗑️</button>
            </td>`;
        tabela.appendChild(tr);
    });
}

// ======== CADASTRAR / EDITAR ========
form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const dados = {
        id_turma: document.getElementById("id_turma").value,
        nome: document.getElementById("nome").value,
        ano: document.getElementById("ano").value,
        turno: document.getElementById("turno").value
    };

    const url = dados.id_turma ? "editar.php" : "cadastrar.php";

    const resp = await fetch(`${apiBase}/${url}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    });

    const resultado = await resp.json();
    alert(resultado.mensagem);
    form.reset();
    document.getElementById("id_turma").value = "";
    carregarTurmas();
});

function editarTurma(id, nome, ano, turno) {
    document.getElementById("id_turma").value = id;
    document.getElementById("nome").value = nome;
    document.getElementById("ano").value = ano;
    document.getElementById("turno").value = turno;
}

cancelarBtn.onclick = () => form.reset();

// ======== EXCLUIR ========
async function excluirTurma(id) {
    if (!confirm("Tem certeza que deseja excluir esta turma?")) return;
    const resp = await fetch(`${apiBase}/excluir.php?id_turma=${id}`);
    const data = await resp.json();
    alert(data.mensagem);
    carregarTurmas();
}

carregarTurmas();

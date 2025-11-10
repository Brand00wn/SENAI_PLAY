const api = "../index.php";

// ---------------- MATRICULAS ----------------
async function listarMatriculas() {
  const res = await fetch(`${api}?action=list_matriculas`);
  const data = await res.json();
  const tbody = document.querySelector("#tabelaMatriculas tbody");
  tbody.innerHTML = "";
  if (!data.success) {
    alert("Erro ao carregar matrículas");
    return;
  }

  data.data.forEach(m => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${m.aluno}</td>
      <td>${m.turma}</td>
      <td>${m.data_matricula}</td>
      <td><button onclick="excluirMatricula(${m.id_matricula})">Excluir</button></td>`;
    tbody.appendChild(tr);
  });
}

async function excluirMatricula(id) {
  if (!confirm("Excluir matrícula?")) return;
  await fetch(`${api}?action=delete_matricula&id_matricula=${id}`);
  listarMatriculas();
}

document.querySelector("#formMatricula").addEventListener("submit", async e => {
  e.preventDefault();
  const id_aluno = document.querySelector("#id_aluno_select").value;
  const id_turma = document.querySelector("#id_turma_select").value;
  const data_matricula = document.querySelector("#data_matricula").value || new Date().toISOString().split("T")[0];
  const res = await fetch(`${api}?action=create_matricula`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id_aluno, id_turma, data_matricula })
  });
  const json = await res.json();
  if (!json.success) alert(json.error || "Erro ao matricular");
  listarMatriculas();
});

// ---------------- SUPORTE ----------------
async function atualizarSelects() {
  const alunos = await fetch(`${api}?action=list_alunos`).then(r => r.json());
  const turmas = await fetch(`${api}?action=list_turmas`).then(r => r.json());

  const selAluno = document.querySelector("#id_aluno_select");
  const selTurma = document.querySelector("#id_turma_select");

  selAluno.innerHTML = alunos.data.map(a => `<option value="${a.id_aluno}">${a.nome}</option>`).join('');
  selTurma.innerHTML = turmas.data.map(t => `<option value="${t.id_turma}">${t.nome_turma}</option>`).join('');
}

// Inicialização
atualizarSelects();
listarMatriculas();

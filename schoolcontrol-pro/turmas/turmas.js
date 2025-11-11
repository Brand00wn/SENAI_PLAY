const API_URL = "./";

document.addEventListener("DOMContentLoaded", () => {
  carregarTurmas();

  const form = document.getElementById("turmaForm");
  const btnReset = document.getElementById("btnReset");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id_turma = document.getElementById("id_turma").value;
    const nome = document.getElementById("nome").value.trim();
    const ano = parseInt(document.getElementById("ano").value);
    const turno = document.getElementById("turno").value;

    const payload = { nome, ano, turno };
    let endpoint = id_turma ? "editar.php" : "cadastrar.php";
    if (id_turma) payload.id_turma = parseInt(id_turma);

    const metodo = id_turma ? "PUT" : "POST";

    const resp = await fetch(API_URL + endpoint, {
      method: metodo,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const data = await resp.json();
    alert(data.mensagem);
    if (data.success) {
      form.reset();
      document.getElementById("id_turma").value = "";
      carregarTurmas();
    }
  });

  btnReset.addEventListener("click", () => {
    form.reset();
    document.getElementById("id_turma").value = "";
  });
});

async function carregarTurmas() {
  const resp = await fetch(API_URL + "listar.php");
  const data = await resp.json();
  const tbody = document.querySelector("#turmasTable tbody");
  tbody.innerHTML = "";

  if (data.success && data.turmas.length) {
    data.turmas.forEach(t => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${t.id_turma}</td>
        <td>${t.nome}</td>
        <td>${t.ano}</td>
        <td>${t.turno || '-'}</td>
        <td>
          <button onclick="editarTurma(${t.id_turma}, '${t.nome}', ${t.ano}, '${t.turno}')">✏️</button>
          <button onclick="excluirTurma(${t.id_turma})">🗑️</button>
        </td>`;
      tbody.appendChild(tr);
    });
  } else {
    tbody.innerHTML = "<tr><td colspan='5'>Nenhuma turma encontrada.</td></tr>";
  }
}

function editarTurma(id, nome, ano, turno) {
  document.getElementById("id_turma").value = id;
  document.getElementById("nome").value = nome;
  document.getElementById("ano").value = ano;
  document.getElementById("turno").value = turno;
  window.scrollTo({ top: 0, behavior: "smooth" });
}

async function excluirTurma(id) {
  if (!confirm("Deseja realmente excluir esta turma?")) return;

  const resp = await fetch(API_URL + "excluir.php?id_turma=" + id, { method: "DELETE" });
  const data = await resp.json();
  alert(data.mensagem);
  if (data.success) carregarTurmas();
}

// turmas.js

document.addEventListener("DOMContentLoaded", () => {
  carregarTurmas();

  const form = document.getElementById("turmaForm");
  const btnReset = document.getElementById("btnReset");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = document.getElementById("id_turma").value;
    const payload = {
      id_turma: id ? parseInt(id, 10) : null,
      nome: document.getElementById("nome").value.trim(),
      ano: parseInt(document.getElementById("ano").value, 10),
      turno: document.getElementById("turno").value
    };

    // Decide qual script PHP chamar
    const url = id ? "editar.php" : "cadastrar.php";

    try {
      const resp = await fetch(url, {
        method: id ? "PUT" : "POST", // PUT para editar, POST para cadastrar
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await resp.json();

      alert(data.mensagem || "Operação realizada.");

      // 🔹 Limpa o formulário e o campo oculto de ID
      form.reset();
      document.getElementById("id_turma").value = "";

      // 🔹 Recarrega a lista de turmas
      carregarTurmas();
    } catch (err) {
      alert("Erro ao salvar turma: " + err.message);
    }
  });

  // 🔹 Botão "Limpar" também zera o campo oculto de ID
  btnReset.addEventListener("click", () => {
    form.reset();
    document.getElementById("id_turma").value = "";
  });
});

// 🔹 Função para carregar todas as turmas
async function carregarTurmas() {
  try {
    const resp = await fetch("listar.php");
    const data = await resp.json();

    if (!data.success) {
      alert(data.mensagem || "Erro ao carregar turmas.");
      return;
    }

    const tbody = document.querySelector("#turmasTable tbody");
    tbody.innerHTML = "";

    if (!data.turmas || data.turmas.length === 0) {
      const tr = document.createElement("tr");
      tr.innerHTML = `<td colspan="5">Nenhuma turma cadastrada.</td>`;
      tbody.appendChild(tr);
      return;
    }

    data.turmas.forEach((t) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${t.id_turma}</td>
        <td>${t.nome}</td>
        <td>${t.ano}</td>
        <td>${t.turno}</td>
        <td>
          <button class="btn btn-success btn-sm" onclick="editarTurma(${t.id_turma}, '${t.nome}', ${t.ano}, '${t.turno}')">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="excluirTurma(${t.id_turma})">Excluir</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error(err);
    alert("Erro ao carregar turmas: " + err.message);
  }
}

// 🔹 Função para excluir uma turma
async function excluirTurma(id) {
  if (!confirm("Deseja realmente excluir esta turma?")) return;

  try {
    const resp = await fetch(`excluir.php?id_turma=${id}`, {
      method: "POST"
    });
    const data = await resp.json();
    alert(data.mensagem);
    carregarTurmas();
  } catch (err) {
    alert("Erro ao excluir turma: " + err.message);
  }
}

// 🔹 Função para preencher o formulário ao clicar em "Editar"
function editarTurma(id, nome, ano, turno) {
  document.getElementById("id_turma").value = id;
  document.getElementById("nome").value = nome;
  document.getElementById("ano").value = ano;
  document.getElementById("turno").value = turno;

  // Rola a tela até o formulário
  window.scrollTo({ top: 0, behavior: "smooth" });
}

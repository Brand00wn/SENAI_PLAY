// turmas.js



document.addEventListener("DOMContentLoaded", () => {
  carregarTurmas();

  const form = document.getElementById("turmaForm");
  const btnReset = document.getElementById("btnReset");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      nome: document.getElementById("nome").value.trim(),
      ano: parseInt(document.getElementById("ano").value, 10),
      turno: document.getElementById("turno").value
    };

    const id = document.getElementById("id_turma").value;

    const url = id
      ? `editar.php?id_turma=${id}`
      : `cadastrar.php`;

    try {
      const resp = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await resp.json();
      alert(data.mensagem || "Operação realizada.");
      form.reset();
      carregarTurmas();
    } catch (err) {
      alert("Erro ao salvar turma: " + err.message);
    }
  });

  btnReset.addEventListener("click", () => form.reset());
});

async function carregarTurmas() {
  try {
    const resp = await fetch(`listar.php`);
    const data = await resp.json();

    if (!data.success) {
      alert(data.mensagem || "Erro ao carregar turmas.");
      return;
    }

    const tbody = document.querySelector("#turmasTable tbody");
    tbody.innerHTML = "";

    if (data.turmas.length === 0) {
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
          <button onclick="editarTurma(${t.id_turma}, '${t.nome}', ${t.ano}, '${t.turno}')">Editar</button>
          <button onclick="excluirTurma(${t.id_turma})">Excluir</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error(err);
    alert("Erro ao carregar turmas: " + err.message);
  }
}

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

function editarTurma(id, nome, ano, turno) {
  document.getElementById("id_turma").value = id;
  document.getElementById("nome").value = nome;
  document.getElementById("ano").value = ano;
  document.getElementById("turno").value = turno;
  window.scrollTo({ top: 0, behavior: "smooth" });
}

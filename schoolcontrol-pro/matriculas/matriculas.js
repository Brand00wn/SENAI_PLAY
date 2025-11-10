// matriculas/matriculas.js
const api = "index.php";

async function fetchJSON(url, options = {}) {
  const res = await fetch(url, options);
  return res.json();
}

// ------------------ CARREGAR SELECTS ------------------
async function atualizarSelects() {
  try {
    const alunos = await fetchJSON(`${api}?action=list_alunos`);
    const turmas = await fetchJSON(`${api}?action=list_turmas`);

    const selAluno = document.querySelector(`#id_aluno_select`);
    const selTurma = document.querySelector(`#id_turma_select`);

    selAluno.innerHTML = `<option value="">Selecione um aluno</option>` +
      alunos.data.map(a => `<option value="${a.id_aluno}">${a.nome}</option>`).join('');

    selTurma.innerHTML = `<option value="">Selecione uma turma</option>` +
      turmas.data.map(t => `<option value="${t.id_turma}">${t.nome_turma}</option>`).join('');

  } catch (e) {
    console.error(`Erro ao carregar selects`, e);
  }
}

// ------------------ LISTAR MATRÍCULAS ------------------
async function listarMatriculas() {
  try {
    const res = await fetchJSON(`${api}?action=list_matriculas`);
    const tbody = document.querySelector(`#tabelaMatriculas tbody`);
    tbody.innerHTML = "";

    res.data.forEach(m => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${m.id_matricula}</td>
        <td>${m.aluno}</td>
        <td>${m.turma}</td>
        <td>${m.data_matricula}</td>
        <td>
          <button class="btn-delete" data-id="${m.id_matricula}">Excluir</button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', async e => {
        const id = e.target.dataset.id;
        await fetchJSON(`${api}?action=delete_matricula&id_matricula=${id}`);
        listarMatriculas();
      });
    });

  } catch (err) {
    console.error("Erro ao listar matrículas", err);
  }
}

// ------------------ ENVIAR FORMULÁRIO ------------------
document.addEventListener("DOMContentLoaded", () => {

  atualizarSelects();
  listarMatriculas();

  const form = document.getElementById("formMatricula");

  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const id_aluno = document.querySelector('#id_aluno_select').value;
      const id_turma = document.querySelector('#id_turma_select').value;

      if (!id_aluno || !id_turma) {
        alert("Selecione um aluno e uma turma!");
        return;
      }

      await fetchJSON(`${api}?action=create_matricula`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_aluno, id_turma })
      });

      listarMatriculas();
    });
  }
});
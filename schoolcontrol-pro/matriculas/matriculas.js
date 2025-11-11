// matriculas.js - adaptado para Matriculas
const api = "index.php";

async function fetchJSON(url, options = {}) {
  const res = await fetch(url, options);
  return res.json();
}

async function atualizarSelects() {
  try {
    const respAlunos = await fetchJSON(`${api}?action=list_alunos`);
    const respTurmas = await fetchJSON(`${api}?action=list_turmas`);

    const alunos = (respAlunos && respAlunos.success) ? respAlunos.data : [];
    const turmas = (respTurmas && respTurmas.success) ? respTurmas.data : [];

    const selAluno = document.querySelector(`#id_aluno_select`);
    const selTurma = document.querySelector(`#id_turma_select`);
    if (!selAluno || !selTurma) return;

    selAluno.innerHTML = '<option value="">-- selecione --</option>';
    selTurma.innerHTML = '<option value="">-- selecione --</option>';

    alunos.forEach(a => {
      const opt = document.createElement('option');
      opt.value = a.id_aluno ?? a.id ?? '';
      opt.textContent = a.nome ?? a.label ?? (`Aluno ${opt.value}`);
      selAluno.appendChild(opt);
    });

    turmas.forEach(t => {
      const opt = document.createElement('option');
      opt.value = t.id_turma ?? t.id ?? '';
      opt.textContent = t.nome ?? t.label ?? (`Turma ${opt.value}`);
      selTurma.appendChild(opt);
    });

  } catch (err) {
    console.error('Erro ao carregar selects:', err);
  }
}

async function listarMatriculas() {
  try {
    const resp = await fetchJSON(`${api}?action=list_matriculas`);
    const tabela = document.querySelector('#list_matriculas tbody');
    if (!tabela) return;
    tabela.innerHTML = '';
    if (!resp || !resp.success || !Array.isArray(resp.data)) {
      tabela.innerHTML = '<tr><td colspan="5">Nenhuma matrícula encontrada.</td></tr>';
      return;
    }

    resp.data.forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${row.id_matricula ?? ''}</td>
        <td>${row.aluno ?? ''}</td>
        <td>${row.turma ?? ''}</td>
        <td>${row.data_matricula ?? ''}</td>
        <td><button class="btn-delete" data-id="${row.id_matricula}">Excluir</button></td>
      `;
      tabela.appendChild(tr);
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.currentTarget.dataset.id;
        if (!confirm('Confirma exclusão?')) return;
        await fetchJSON(`${api}?action=delete_matricula&id_matricula=${id}`);
        listarMatriculas();
      });
    });

  } catch (err) {
    console.error('Erro ao listar matriculas:', err);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  atualizarSelects();
  listarMatriculas();

  const form = document.querySelector('form');
  if (!form) return;
  form.addEventListener('submit', async (e) => {
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
      body: JSON.stringify({ id_aluno: parseInt(id_aluno), id_turma: parseInt(id_turma) })
    });
    listarMatriculas();
    document.querySelector('#id_aluno_select').value = '';
    document.querySelector('#id_turma_select').value = '';
  });
});

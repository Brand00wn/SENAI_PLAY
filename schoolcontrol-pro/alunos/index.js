// ========== FILTRAR ALUNOS ==========
document.getElementById('filtro').addEventListener('keyup', function() {
    const filtro = this.value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaAlunos tbody tr');
  
    linhas.forEach(linha => {
      const texto = linha.textContent.toLowerCase();
      linha.style.display = texto.includes(filtro) ? '' : 'none';
    });
  });
  
  // ========== ORDENAR TABELA ==========
  document.querySelectorAll('#tabelaAlunos th').forEach(th => {
    th.addEventListener('click', () => {
      const tabela = th.closest('table');
      const tbody = tabela.querySelector('tbody');
      const index = parseInt(th.getAttribute('data-col'));
      const asc = th.classList.toggle('asc');
  
      [...tbody.rows]
        .sort((a, b) => {
          const v1 = a.cells[index].innerText.trim();
          const v2 = b.cells[index].innerText.trim();
          return asc ? v1.localeCompare(v2, 'pt-BR') : v2.localeCompare(v1, 'pt-BR');
        })
        .forEach(row => tbody.appendChild(row));
    });
  });
  
  // ========== EXPORTAR PARA CSV ==========
  document.getElementById('exportarCSV').addEventListener('click', () => {
    const linhas = document.querySelectorAll('#tabelaAlunos tr');
    let csv = [];
  
    linhas.forEach(linha => {
      const cols = linha.querySelectorAll('th, td');
      const dados = Array.from(cols).map(td => '"' + td.innerText.replace(/"/g, '""') + '"');
      csv.push(dados.join(';'));
    });
  
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'alunos.csv';
    link.click();
  });
  
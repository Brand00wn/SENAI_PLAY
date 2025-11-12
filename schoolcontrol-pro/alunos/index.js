const tabela = document.querySelector("#tabelaAlunos tbody");
const form = document.getElementById("formAluno");
const btnSalvar = document.getElementById("btnSalvar");
const btnCancelar = document.getElementById("btnCancelar");

// O caminho da API deve ser ajustado conforme a sua estrutura de pastas
const apiUrl = "./index.php"; 

let editando = false;
var alunoIdParaEdicao = null; // Armazena o ID do aluno que está sendo editado

// Função para listar os alunos
function listarAlunos() {
  fetch(`${apiUrl}?acao=listar`)
    .then(res => {
        if (!res.ok) {
            throw new Error('Erro na requisição: ' + res.statusText);
        }
        return res.json();
    })
    .then(data => {
      tabela.innerHTML = "";

      // Verifica se a resposta é um erro (caso o PHP retorne um objeto com 'error')
      if (data.error) {
        tabela.innerHTML = `<tr><td colspan="5">Erro: ${data.error}</td></tr>`;
        return;
      }

      // Se a resposta for um array vazio, exibe a mensagem
      if (data.length === 0) {
        tabela.innerHTML = "<tr><td colspan='5'>Nenhum aluno cadastrado.</td></tr>";
        return;
      }

      // Popula a tabela
      data.forEach(aluno => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${aluno.id_aluno}</td>
          <td>${aluno.nome}</td>
          <td>${aluno.email}</td>
          <td>${new Date(aluno.data_nascimento + 'T00:00:00').toLocaleDateString('pt-BR')}</td>
          <td>
            <button class="btn-editar" data-id="${aluno.id_aluno}" onclick="buscarAlunoParaEdicao(${aluno.id_aluno})">Editar</button>
            <button class="btn-excluir" data-id="${aluno.id_aluno}" onclick="excluirAluno(${aluno.id_aluno})">Excluir</button>
          </td>
        `;
        tabela.appendChild(tr);
      });
    })
    .catch(error => {
      console.error("Erro ao carregar os alunos:", error);
      tabela.innerHTML = `<tr><td colspan='5'>Erro ao carregar os alunos: ${error.message}</td></tr>`;
    });
}

// Função de cadastro/edição (chamada pelo submit do formulário)
form.addEventListener("submit", function(e) {
  e.preventDefault();

  const formData = new FormData(form);
  const acao = editando ? 'editar' : 'cadastrar';

  // Adiciona o ID do aluno ao FormData se estiver editando
  if (editando) {
      formData.append('id', alunoIdParaEdicao);
  }

  fetch(`${apiUrl}?acao=${acao}`, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(res => {
    if (res.error) {
      alert("Erro: " + res.error);
    } else {
      alert(res.message || (editando ? "Aluno atualizado!" : "Aluno cadastrado!"));
      
      // Limpa o formulário e reseta o estado de edição
      form.reset();
      editando = false;
      alunoIdParaEdicao = null;
      document.getElementById("id").value = ""; // Limpa o campo oculto
      btnSalvar.textContent = "Cadastrar";
      btnCancelar.style.display = "none";
      
      listarAlunos(); // Recarrega a lista
    }
  })
  .catch(error => {
      console.error("Erro na requisição:", error);
      alert("Erro na requisição. Verifique o console para mais detalhes.");
  });
});

// Função para buscar os dados de um aluno específico para edição
function buscarAlunoParaEdicao(id) {
    // Busca todos os alunos e filtra, como no código original. 
    // O ideal seria ter uma ação 'buscar' na API, mas mantive a lógica original para simplificar.
    fetch(`${apiUrl}?acao=listar`)
        .then(res => res.json())
        .then(data => {
            const aluno = data.find(a => a.id_aluno == id);
            if (!aluno) return alert("Aluno não encontrado");

            // Preenche o formulário
            document.getElementById("id").value = aluno.id_aluno;
            document.getElementById("nome").value = aluno.nome;
            document.getElementById("email").value = aluno.email;
            
            // O campo data_nascimento deve ser preenchido no formato YYYY-MM-DD
            // O PHP retorna a data no formato correto (se for um campo DATE)
            document.getElementById("data_nascimento").value = aluno.data_nascimento; 

            // Define o estado de edição
            editando = true;
            alunoIdParaEdicao = aluno.id_aluno;
            btnSalvar.textContent = "Salvar Alterações";
            btnCancelar.style.display = "inline";
        })
        .catch(error => {
            console.error("Erro ao buscar aluno para edição:", error);
            alert("Erro ao buscar aluno para edição.");
        });
}

// Cancelar edição
btnCancelar.addEventListener("click", () => {
  form.reset();
  editando = false;
  alunoIdParaEdicao = null;
  document.getElementById("id").value = ""; // Limpa o campo oculto
  btnSalvar.textContent = "Cadastrar";
  btnCancelar.style.display = "none";
});

// Excluir aluno
function excluirAluno(id) {
  if (!confirm("Deseja realmente excluir este aluno?")) return;

  fetch(`${apiUrl}?acao=excluir&id=${id}`)
    .then(res => res.json())
    .then(res => {
      if (res.error) {
        alert("Erro ao excluir: " + res.error);
      } else {
        alert(res.message || "Aluno excluído!");
        listarAlunos();
      }
    })
    .catch(error => {
        console.error("Erro na requisição de exclusão:", error);
        alert("Erro na requisição de exclusão. Verifique o console para mais detalhes.");
    });
}

// Carrega a lista ao iniciar
listarAlunos();

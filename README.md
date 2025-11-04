# 🏫 SchoolControl Pro  
### Sistema de Controle Escolar — Trabalho Colaborativo (PHP + MySQL + JS + HTML + CSS)

---

## 🎯 Objetivo Geral
Desenvolver, em equipes, um **sistema web de controle escolar** utilizando as linguagens:

> 🧩 **HTML, CSS, JavaScript, PHP e MySQL**

O projeto deve permitir **cadastrar, listar, editar e excluir dados** de alunos, professores, disciplinas, turmas e matrículas, aplicando **CRUDs reais com banco de dados** e interface web funcional.

Além do código, o trabalho avaliará **uso do Git/GitHub**, **organização em equipe**, **boas práticas de documentação** e **integração entre os módulos**.

---

## 💻 Descrição Geral do Sistema
O **SchoolControl Pro** simula o sistema administrativo de uma escola técnica.  
Será dividido em **módulos interligados**, desenvolvidos por diferentes grupos e integrados em um único projeto hospedado no **GitHub da turma**.

### ⚙️ Tecnologias obrigatórias

| Camada | Tecnologia |
|:--------|:------------|
| Front-end | HTML5, CSS3, JavaScript |
| Back-end | PHP (puro ou estruturado) |
| Banco de Dados | MySQL (phpMyAdmin / XAMPP) |
| Versionamento | Git e GitHub (branch → pull request → merge) |

---

## 🧱 Módulos do Sistema

### 👩‍🎓 1. Alunos (CRUD)
- Cadastrar, listar, editar e excluir alunos.  
- Campos: `id_aluno`, `nome`, `email`, `data_nascimento`.  
- Validar e-mails válidos e impedir duplicidades.  
- Exibir mensagens de erro e sucesso.

---

### 👨‍🏫 2. Professores (CRUD)
- Cadastrar, listar, editar e excluir professores.  
- Campos: `id_professor`, `nome`, `email`, `telefone`.  
- Validação de e-mail e telefone.  
- Listagem e busca por nome.

---

### 📚 3. Disciplinas (CRUD)
- Cadastrar, listar, editar e excluir disciplinas.  
- Campos: `id_disciplina`, `nome`, `carga_horaria`.  
- Impedir nomes duplicados e validar carga horária maior que zero.

---

### 🏫 4. Turmas (CRUD + vínculo com Disciplinas)
- Cadastrar, listar, editar e excluir turmas.  
- Campos: `id_turma`, `nome`, `ano`, `turno`.  
- Vincular várias disciplinas à turma (tabela associativa).  
- Exibir disciplinas vinculadas na listagem.

---

### 🧾 5. Matrículas (Aluno → Turma + Disciplinas)
- Criar matrícula relacionando **Aluno**, **Turma** e as **Disciplinas da Turma**.  
- Impedir matrícula duplicada (mesmo aluno na mesma turma).  
- Exibir matrículas por aluno e por turma.

---

## 🧩 Modelagem do Banco de Dados (sugestão)

| Tabela | Campos principais |
|:--------|:------------------|
| **alunos** | id_aluno (PK), nome, email, data_nascimento |
| **professores** | id_professor (PK), nome, email, telefone |
| **disciplinas** | id_disciplina (PK), nome, carga_horaria |
| **turmas** | id_turma (PK), nome, ano, turno |
| **turmas_disciplinas** | id_turma (FK), id_disciplina (FK) |
| **matriculas** | id_matricula (PK), id_aluno (FK), id_turma (FK), data_matricula |
| **matriculas_disciplinas** | id_matricula (FK), id_disciplina (FK) |

> 🔹 Utilize **chaves estrangeiras** com integridade referencial (`ON DELETE CASCADE`, quando aplicável).  
> 🔹 Exporte o banco em um arquivo chamado `script_bd.sql`.

---

<summary><strong>Estrutura do Projeto</strong></summary>

  <pre><code>schoolcontrol-pro/
├─ config/
│  └─ conexao.php
├─ includes/
│  ├─ header.php
│  ├─ menu.php
│  └─ footer.php
├─ assets/
│  ├─ css/
│  │  └─ style.css
│  ├─ js/
│  │  └─ script.js
│  └─ img/
├─ alunos/
│  ├─ listar.php
│  ├─ cadastrar.php
│  ├─ editar.php
│  └─ excluir.php
├─ professores/
│  ├─ listar.php
│  ├─ cadastrar.php
│  ├─ editar.php
│  └─ excluir.php
├─ disciplinas/
│  ├─ listar.php
│  ├─ cadastrar.php
│  ├─ editar.php
│  └─ excluir.php
├─ turmas/
│  ├─ listar.php
│  ├─ cadastrar.php
│  ├─ editar.php
│  ├─ excluir.php
│  └─ vincular_disciplinas.php
├─ matriculas/
│  ├─ listar.php
│  ├─ cadastrar.php
│  └─ excluir.php
├─ index.php
├─ script_bd.sql
└─ README.md
</code></pre>

## 🚀 Como Rodar o Projeto

1. **Instale o XAMPP** e inicie o **Apache** e o **MySQL**.  
2. Copie o projeto para a pasta:
   C:\xampp\htdocs\schoolcontrol-pro
3. No **phpMyAdmin**:
- Crie o banco de dados `schoolcontrol`;
- Importe o arquivo `script_bd.sql`.  
4. Acesse no navegador:
  http://localhost/schoolcontrol-pro
5. O sistema exibirá o menu com os módulos:  
**Alunos | Professores | Disciplinas | Turmas | Matrículas**

---

## 🤝 Organização e Colaboração (GitHub)

- **Branch principal:** `main`  
- **Branch de integração:** `dev`  
- **Branches por módulo:** `feature/alunos`, `feature/professores`, etc.  

### 🔁 Fluxo de Trabalho
1. Crie uma *issue* descrevendo sua tarefa.  
2. Crie uma *branch*:  
   ```git checkout -b feature/<modulo>```
3. Faça commits pequenos e claros:
   adiciona validação de e-mail
4. Envie para o GitHub:
   ```git push origin feature/alunos```
5. Abra um Pull Request (PR) para a branch dev.
6. Outro grupo revisa o PR antes do merge.
7. Após aprovação, o professor fará o merge final para main.

🧠 Regras de Negócio
- IDs únicos e incrementais.
- Validação antes de inserir ou editar.
- Mensagens de sucesso e erro claras.
- Confirmação antes de excluir.
- Impedir exclusão de aluno/turma se houver matrícula.
- Ordenar listagens alfabeticamente.
- Layout unificado entre os módulos.

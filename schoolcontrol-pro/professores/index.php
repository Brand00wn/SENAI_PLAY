<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Cadastro Professores</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <nav>
    <ul>
    <li><a href="index.php">Início</a></li>
      <li><a href="alunos/index.php">Alunos</a></li>
      <li><a href="turmas/index.php">Turmas</a></li>
      <li><a href="disciplinas/index.php">Disciplinas</a></li>
      <li><a href="professores/index.php" class="active">Professores</a></li>
      <li><a href="matriculas/index.php">Matrículas</a></li>
    
    </ul>
  </nav>

  <h1>Cadastro De Professores</h1>

  <form id="formprof">
    <input type="hidden" id="id" />
    <label>Nome:
      <input type="text" id="nome" required />
    </label>
    <label>Disciplina:
      <input type="text" id="dis" required />
    </label>
    <label>Email:
      <input type="email" id="email" required />
    </label>
    <label>Telefone:
      <input type="number" id="tel" required />
    </label>
    <button type="submit">Salvar</button>
  </form>

  <h2>Lista de Professores</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Disciplina</th>
        <th>Email</th>
        <th>Telefone</th>
       
      
    </thead>
    <tbody id="listaprof"></tbody>
  </table>

  <script src="scriptpf.js" defer></script>
</body>
</html>
<?php
session_start();

// Inicializa o array de disciplinas na sessão, se ainda não existir
if (!isset($_SESSION['disciplinas'])) {
  $_SESSION['disciplinas'] = [];
}

// Cadastra nova disciplina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $carga = intval($_POST['carga'] ?? 0);

  if ($nome && $carga > 0) {
    $id = count($_SESSION['disciplinas']) + 1;
    $_SESSION['disciplinas'][] = [
      'id' => $id,
      'nome' => htmlspecialchars($nome),
      'carga' => $carga
    ];
  }
}

// Excluir disciplina
if (isset($_GET['excluir'])) {
  $idExcluir = intval($_GET['excluir']);
  $_SESSION['disciplinas'] = array_values(array_filter($_SESSION['disciplinas'], fn($d) => $d['id'] !== $idExcluir));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disciplinas - SchoolControl</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <div class="container">
    <h1>Gerenciamento de Disciplinas</h1>

    <form id="formDisciplina" method="POST" action="">
      <label>Nome da Disciplina:</label>
      <input type="text" name="nome" id="nome" required>
      
      <label>Carga Horária:</label>
      <input type="number" name="carga" id="carga" min="1" required>

      <button type="submit">Cadastrar</button>
    </form>

    <h2>Lista de Disciplinas</h2>
    <table id="tabela">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Carga Horária</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($_SESSION['disciplinas'])): ?>
          <tr><td colspan="4">Nenhuma disciplina cadastrada.</td></tr>
        <?php else: ?>
          <?php foreach ($_SESSION['disciplinas'] as $d): ?>
            <tr>
              <td><?= $d['id'] ?></td>
              <td><?= $d['nome'] ?></td>
              <td><?= $d['carga'] ?> h</td>
              <td><a href="?excluir=<?= $d['id'] ?>" onclick="return confirm('Deseja excluir esta disciplina?')">Excluir</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>

<?php
session_start();

// ✅ Carrega disciplinas iniciais apenas na primeira vez da sessão
if (!isset($_SESSION['disciplinas'])) {
  $_SESSION['disciplinas'] = [
    ['id' => 1, 'nome' => 'Matemática', 'carga' => 80],
    ['id' => 2, 'nome' => 'Português', 'carga' => 60],
    ['id' => 3, 'nome' => 'Ciências', 'carga' => 70],
    ['id' => 4, 'nome' => 'Inglês', 'carga' => 50],
  ];
}

// Editar disciplina - carrega os dados no formulário
$editando = false;
$disciplinaEdit = null;
if (isset($_GET['editar'])) {
  $idEditar = intval($_GET['editar']);
  foreach ($_SESSION['disciplinas'] as $d) {
    if ($d['id'] === $idEditar) {
      $disciplinaEdit = $d;
      $editando = true;
      break;
    }
  }
}

// Salvar alterações ou cadastrar nova disciplina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  $carga = intval($_POST['carga'] ?? 0);
  $id = intval($_POST['id'] ?? 0);

  if ($nome && $carga > 0) {
    if ($id > 0) {
      // Atualizar disciplina existente
      foreach ($_SESSION['disciplinas'] as &$d) {
        if ($d['id'] === $id) {
          $d['nome'] = htmlspecialchars($nome);
          $d['carga'] = $carga;
          break;
        }
      }
      unset($d);
    } else {
      // Gerar novo ID sem duplicar
      $ids = array_column($_SESSION['disciplinas'], 'id');
      $novoId = empty($ids) ? 1 : max($ids) + 1;

      $_SESSION['disciplinas'][] = [
        'id' => $novoId,
        'nome' => htmlspecialchars($nome),
        'carga' => $carga
      ];
    }
  }

  // Redireciona para limpar POST e evitar duplicidade
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Excluir disciplina
if (isset($_GET['excluir'])) {
  $idExcluir = intval($_GET['excluir']);
  $_SESSION['disciplinas'] = array_values(
    array_filter($_SESSION['disciplinas'], fn($d) => $d['id'] !== $idExcluir)
  );

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciamento de Disciplinas</title>
</head>
<body>
  <h1>Gerenciamento de Disciplinas</h1>

  <form method="POST" action="">
    <input type="hidden" name="id" value="<?= $disciplinaEdit['id'] ?? 0 ?>">

    <label>Nome da Disciplina:</label><br>
    <input type="text" name="nome" required value="<?= $disciplinaEdit['nome'] ?? '' ?>"><br><br>

    <label>Carga Horária:</label><br>
    <input type="number" name="carga" min="1" required value="<?= $disciplinaEdit['carga'] ?? '' ?>"><br><br>

    <button type="submit"><?= $editando ? 'Salvar Alterações' : 'Cadastrar' ?></button>

    <?php if ($editando): ?>
      <a href="<?= $_SERVER['PHP_SELF'] ?>">Cancelar Edição</a>
    <?php endif; ?>
  </form>

  <h2>Lista de Disciplinas</h2>
  <table border="1" cellpadding="5" cellspacing="0">
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
            <td>
              <a href="?editar=<?= $d['id'] ?>">Editar</a> |
              <a href="?excluir=<?= $d['id'] ?>" onclick="return confirm('Deseja excluir esta disciplina?')">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>

    <script src="disciplinas.js"></script>
  
  </table>
</body>
</html>
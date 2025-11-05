<?php
// =====================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// =====================================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sistema_escolar";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Erro de conexão: " . $conn->connect_error);
}

$msg = "";
$erro = "";

// =====================================
// CADASTRAR ALUNO
// =====================================
if (isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
  $nome = trim($_POST['nome']);
  $email = trim($_POST['email']);
  $data = $_POST['data_nascimento'];

  // Verifica duplicidade de email
  $check = $conn->prepare("SELECT id FROM alunos WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $erro = "Este e-mail já está cadastrado!";
  } else {
    $stmt = $conn->prepare("INSERT INTO alunos (nome, email, data_nascimento) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $data);
    if ($stmt->execute()) {
      $msg = "Aluno cadastrado com sucesso!";
    } else {
      $erro = "Erro ao cadastrar aluno.";
    }
  }
}

// =====================================
// EDITAR ALUNO
// =====================================
if (isset($_POST['acao']) && $_POST['acao'] == 'editar') {
  $id = $_POST['id'];
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $data = $_POST['data_nascimento'];

  $check = $conn->prepare("SELECT id FROM alunos WHERE email = ? AND id != ?");
  $check->bind_param("si", $email, $id);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $erro = "Este e-mail já pertence a outro aluno!";
  } else {
    $stmt = $conn->prepare("UPDATE alunos SET nome=?, email=?, data_nascimento=? WHERE id=?");
    $stmt->bind_param("sssi", $nome, $email, $data, $id);
    if ($stmt->execute()) {
      $msg = "Aluno atualizado com sucesso!";
    } else {
      $erro = "Erro ao atualizar aluno.";
    }
  }
}

// =====================================
// EXCLUIR ALUNO
// =====================================
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM alunos WHERE id = $id");
  $msg = "Aluno excluído com sucesso!";
}

// =====================================
// CARREGAR DADOS PARA EDIÇÃO
// =====================================
$editando = false;
$alunoEditar = [];
if (isset($_GET['edit'])) {
  $editando = true;
  $id = $_GET['edit'];
  $alunoEditar = $conn->query("SELECT * FROM alunos WHERE id = $id")->fetch_assoc();
}

// =====================================
// LISTAR ALUNOS
// =====================================
$result = $conn->query("SELECT * FROM alunos ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>CRUD de Alunos</title>
  <script>
    function confirmarExclusao() {
      return confirm("Tem certeza que deseja excluir este aluno?");
    }
  </script>
</head>
<body>
  <h1>Sistema Escolar - CRUD de Alunos</h1>

  <?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
  <?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>

  <h2><?= $editando ? "Editar Aluno" : "Cadastrar Novo Aluno" ?></h2>

  <form method="POST">
    <?php if ($editando): ?>
      <input type="hidden" name="id" value="<?= $alunoEditar['id'] ?>">
    <?php endif; ?>

    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?= $editando ? htmlspecialchars($alunoEditar['nome']) : '' ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= $editando ? htmlspecialchars($alunoEditar['email']) : '' ?>" required><br><br>

    <label>Data de Nascimento:</label><br>
    <input type="date" name="data_nascimento" value="<?= $editando ? $alunoEditar['data_nascimento'] : '' ?>" required><br><br>

    <button type="submit" name="acao" value="<?= $editando ? 'editar' : 'cadastrar' ?>">
      <?= $editando ? 'Salvar Alterações' : 'Cadastrar' ?>
    </button>

    <?php if ($editando): ?>
      <a href="alunos.php">Cancelar</a>
    <?php endif; ?>
  </form>

  <hr>

  <h2>Lista de Alunos</h2>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th>Email</th>
      <th>Data de Nascimento</th>
      <th>Ações</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['nome']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= date('d/m/Y', strtotime($row['data_nascimento'])) ?></td>
        <td>
          <a href="?edit=<?= $row['id'] ?>">Editar</a> |
          <a href="?delete=<?= $row['id'] ?>" onclick="return confirmarExclusao();">Excluir</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

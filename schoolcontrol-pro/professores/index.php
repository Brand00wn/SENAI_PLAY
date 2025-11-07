<?php

$host = "localhost";
$user = "root";
$pass = "alunolab";
$dbname = "schoolcontropro";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";
    $nome = $_POST["nome"] ?? "";
    $dis = $_POST["dis"] ?? "";
    $email = $_POST["email"] ?? "";
    $tel = $_POST["tel"] ?? "";

    if ($id) {
        $sql = "UPDATE professor SET nome=?, disciplina=?, email=?, telefone=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $dis, $email, $tel, $id);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO professor (nome, disciplina, email, telefone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $dis, $email, $tel);
        $stmt->execute();
    }

    header("Location: index.php");
    exit;
}


if (isset($_GET["excluir"])) {
    $id = intval($_GET["excluir"]);
    $conn->query("DELETE FROM professor WHERE id=$id");
    header("Location: index.php");
    exit;
}


$editar = null;
if (isset($_GET["editar"])) {
    $id_edit = intval($_GET["editar"]);
    $result_edit = $conn->query("SELECT * FROM professor WHERE id=$id_edit");
    if ($result_edit->num_rows > 0) {
        $editar = $result_edit->fetch_assoc();
    }
}


$result = $conn->query("SELECT * FROM professor ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Cadastro professor - School Contro Pro</title>
  <link rel="stylesheet" href="../assets/CSS/style.css" />
</head>
<body>
  <header>
    <h1>Sistema de Cadastro Acadêmico</h1>
  </header>

  <nav>
    <ul>
      <li><a href="../index.php">Início</a></li>
      <li><a href="../alunos/index.php">Alunos</a></li>
      <li><a href="../turmas/index.php">Turmas</a></li>
      <li><a href="../disciplinas/index.php">Disciplinas</a></li>
      <li><a href="index.php" class="active">professor</a></li>
      <li><a href="../matriculas/index.php">Matrículas</a></li>
    </ul>
  </nav>

  <main>
    <h1>Cadastro de professor</h1>

    <form id="formprof" method="POST" action="">
      <input type="hidden" name="id" id="id" value="<?= $editar['id'] ?? '' ?>" />

      <label>Nome:
        <input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($editar['nome'] ?? '') ?>" />
      </label>

      <label>Disciplina:
        <input type="text" name="dis" id="dis" required value="<?= htmlspecialchars($editar['disciplina'] ?? '') ?>" />
      </label>

      <label>Email:
        <input type="email" name="email" id="email" required value="<?= htmlspecialchars($editar['email'] ?? '') ?>" />
      </label>

      <label>Telefone:
        <input type="number" name="tel" id="tel" required value="<?= htmlspecialchars($editar['telefone'] ?? '') ?>" />
      </label>

      <button type="submit"><?= $editar ? 'Atualizar' : 'Salvar' ?></button>
      <?php if ($editar): ?>
        <a href="index.php" style="margin-left: 10px; text-decoration:none; color:red;">Cancelar edição</a>
      <?php endif; ?>
    </form>

    <h2>Lista de Professor</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Disciplina</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row["id"]) ?></td>
            <td><?= htmlspecialchars($row["nome"]) ?></td>
            <td><?= htmlspecialchars($row["disciplina"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
            <td><?= htmlspecialchars($row["telefone"]) ?></td>
            <td>
              <a href="?editar=<?= $row["id"] ?>">Editar</a> |
              <a href="?excluir=<?= $row["id"] ?>" onclick="return confirm('Deseja realmente excluir este professor?')">Excluir</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; 2025 School Contro Pro — Sistema de Cadastro Acadêmico</p>
  </footer>
</body>
</html>

<?php
$conn->close();
?>

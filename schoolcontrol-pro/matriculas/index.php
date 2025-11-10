<?php
require_once "../config/conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_aluno'], $_POST['id_turma'])) {
    $id_aluno = $_POST['id_aluno'];
    $id_turma = $_POST['id_turma'];
    $data_matricula = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO matriculas (id_aluno, id_turma, data_matricula) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_aluno, $id_turma, $data_matricula);
    $stmt->execute();
    $stmt->close();
}

// Puxa alunos existentes
$alunos = $conn->query("SELECT id_aluno, nome FROM alunos ORDER BY nome");

// Puxa turmas existentes
$turmas = $conn->query("SELECT id_turma, nome FROM turmas ORDER BY nome");

$matriculas = $conn->query("
    SELECT m.id_matricula, a.nome AS aluno, t.nome AS turma, m.data_matricula
    FROM matriculas m
    JOIN alunos a ON a.id_aluno = m.id_aluno
    JOIN turmas t ON t.id_turma = m.id_turma
    ORDER BY m.id_matricula DESC
");

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Eventos</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
</head>
<body>

<div>
    <header>
        <h1>Sistema de Cadastro Acadêmico</h1>
    </header>

    <nav>
        <ul>
        <li><a href="index.php" >Inicio</a></li>
        <li><a href="alunos/index.php" >Alunos</a></li>
        <li><a href="turmas/index.php" >Turmas</a></li>
        <li><a href="disciplinas/index.php">Disciplinas</a></li>
        <li><a href="professores/index.php">Professores</a></li>
        <li><a href="matriculas/index.php" class="active">Matrículas</a></li>
        
        </ul>
    </nav>
  
    <div>
        <h2>Matricula</h2>
        <form action="listar.php" method="POST">

       <label for="aluno">Aluno:</label>
        <select id="aluno" name="id_aluno" required>
            <option value="">Selecione um aluno</option>
            <?php while ($a = $alunos->fetch_assoc()): ?>
                <option value="<?= $a['id_aluno'] ?>"><?= htmlspecialchars($a['nome']) ?></option>
            <?php endwhile; ?>
        </select>

        <br><br>

        <label for="turma">Turma:</label>
        <select id="turma" name="id_turma" required>
            <option value="">Selecione uma turma</option>
            <?php while ($t = $turmas->fetch_assoc()): ?>
                <option value="<?= $t['id_turma'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
            <?php endwhile; ?>
        </select>

        <br><br>

        <button type="submit">Salvar Matrícula</button>

</form>
    </div>

    <div >
       <!-- Tabela de matrículas já feitas -->
    <h2>Matrículas Realizadas</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Data da Matrícula</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($matriculas && $matriculas->num_rows > 0): ?>
                <?php while ($m = $matriculas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $m['id_matricula'] ?></td>
                        <td><?= htmlspecialchars($m['aluno']) ?></td>
                        <td><?= htmlspecialchars($m['turma']) ?></td>
                        <td><?= date("d/m/Y", strtotime($m['data_matricula'])) ?></td>
                        <td>
                            <a href="editar_matricula.php?id=<?= $m['id_matricula'] ?>">Editar</a> |
                            <a href="excluir_matricula.php?id=<?= $m['id_matricula'] ?>" onclick="return confirm('Deseja realmente excluir?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhuma matrícula encontrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

</body>
</html>
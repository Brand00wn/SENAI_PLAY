<?php
include 'conexao.php';

$sql = "
SELECT 
    m.id_matricula AS matricula_id,
    a.nome AS aluno,
    t.nome AS turma,
    d.nome AS disciplina,
    p.nome AS professor,
    m.data_matricula
FROM matriculas m
JOIN alunos a ON a.id_aluno = m.id_aluno
JOIN turmas t ON t.id_turma = m.id_turma
JOIN turmas_disciplina td ON td.id_turma = t.id_turma
JOIN disciplina d ON d.id_disciplina = td.id_disciplina
JOIN professor p ON p.id_professor = d.id_professor
ORDER BY a.nome, t.nome, d.nome
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Matrículas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Listagem de Matrículas</h1>

    <div class="filtros">
        <input type="text" id="busca" placeholder="Buscar por aluno, turma, disciplina ou professor...">
        <select id="filtroTurma">
            <option value="">Filtrar por turma</option>
            <?php
            $turmas = $conn->query("SELECT DISTINCT nome FROM turmas ORDER BY nome");
            while ($t = $turmas->fetch_assoc()) {
                echo "<option value='{$t['nome']}'>{$t['nome']}</option>";
            }
            ?>
        </select>
    </div>

    <table id="tabelaMatriculas">
        <thead>
            <tr>
                <th>ID Matrícula</th>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Data da Matrícula</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['matricula_id'] ?></td>
                        <td><?= htmlspecialchars($row['aluno']) ?></td>
                        <td><?= htmlspecialchars($row['turma']) ?></td>
                        <td><?= htmlspecialchars($row['disciplina']) ?></td>
                        <td><?= htmlspecialchars($row['professor']) ?></td>
                        <td><?= date("d/m/Y", strtotime($row['data_matricula'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhuma matrícula encontrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script src="assets/JS/script.js"></script>
</body>
</html>

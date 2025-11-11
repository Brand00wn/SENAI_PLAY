<?php
include '../config/conexao.php';
$sql = "
SELECT 
    m.id_matricula,
    a.nome AS aluno,
    t.nome AS turma,
    m.data_matricula
FROM matriculas m
JOIN alunos a ON a.id_aluno = m.id_aluno
JOIN turmas t ON t.id_turma = m.id_turma
ORDER BY m.data_matricula DESC
";
$res = $conn->query($sql);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Listar Matrículas</title></head><body>
<h1>Listar Matrículas</h1>
<table border="1">
<thead><tr><th>ID</th><th>Aluno</th><th>Turma</th><th>Data</th></tr></thead>
<tbody>
<?php if ($res && $res->num_rows): while($row = $res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['id_matricula']) ?></td>
<td><?= htmlspecialchars($row['aluno']) ?></td>
<td><?= htmlspecialchars($row['turma']) ?></td>
<td><?= htmlspecialchars($row['data_matricula']) ?></td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="4">Nenhuma matrícula encontrada.</td></tr>
<?php endif; ?>
</tbody>
</table>
</body></html>

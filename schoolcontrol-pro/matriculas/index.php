<?php
require_once "config/conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Eventos</title>
    <link rel="stylesheet" href="assets/CSS/style.css">
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
        <form action="listar.php" method="post">
            <label>Aluno:</label>
            <input type="text" name="nome" required>

            <label>Turma:</label>
            <input type="text" name="tipo" required>

            <label></label>
            <input type="date" name="data" required>

            <input type="submit" value="Cadastrar">
        </form> 
    </div>

    <div >
        <h2>Lista de Eventos</h2>

        <?php
        $sql = "SELECT * FROM evento";
        $resultado = $conexao->query($sql);

        if ($resultado->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Data</th></tr>";

            while($linha = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $linha["id"] . "</td>";
                echo "<td>" . $linha["aluno"] . "</td>";
                echo "<td>" . $linha["turma"] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Nenhum evento encontrado.</p>";
        }

        $conexao->close();
        ?>
    </div>
</div>

</body>
</html>
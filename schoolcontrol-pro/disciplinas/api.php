<?php
header("Content-Type: application/json");
require_once("../config/conexao.php");

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $sql = "SELECT * FROM disciplina";
    $resultado = $conn->query($sql);
    echo json_encode($resultado->fetch_all(MYSQLI_ASSOC));
}

elseif ($metodo === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);
    $id = $dados['id'] ?? '';
    $nome = $dados['nome'];
    $carga = $dados['carga'];

    if ($id == '') {
        $stmt = $conn->prepare("INSERT INTO disciplina (nome, carga_horaria) VALUES (?, ?)");
        $stmt->bind_param("si", $nome, $carga);
        $stmt->execute();
        echo json_encode(["mensagem" => "Disciplina adicionada com sucesso!"]);
    } else {
        $stmt = $conn->prepare("UPDATE disciplina SET nome=?, carga_horaria=? WHERE id_disciplina=?");
        $stmt->bind_param("sii", $nome, $carga, $id);
        $stmt->execute();
        echo json_encode(["mensagem" => "Disciplina atualizada com sucesso!"]);
    }
}

elseif ($metodo === 'DELETE') {
    $dados = json_decode(file_get_contents("php://input"), true);
    $id = $dados['id'];
    $stmt = $conn->prepare("DELETE FROM disciplina WHERE id_disciplina=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["mensagem" => "Disciplina excluída com sucesso!"]);
}

$conn->close();
?>

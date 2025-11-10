<?php
header("Content-Type: application/json; charset=utf-8");
require_once "../../config/conexao.php";

$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados || empty($dados["nome"]) || empty($dados["ano"])) {
    echo json_encode(["mensagem" => "Preencha todos os campos obrigatórios."]);
    exit;
}

try {
    // Evita duplicar turma com mesmo nome e ano
    $verifica = $conn->prepare("SELECT COUNT(*) FROM turmas WHERE nome = ? AND ano = ?");
    $verifica->execute([$dados["nome"], $dados["ano"]]);
    if ($verifica->fetchColumn() > 0) {
        echo json_encode(["mensagem" => "Já existe uma turma com esse nome e ano."]);
        exit;
    }

    $sql = "INSERT INTO turmas (nome, ano, turno) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $dados["nome"],
        $dados["ano"],
        $dados["turno"] ?? null
    ]);

    echo json_encode(["mensagem" => "Turma cadastrada com sucesso!"]);
} catch (PDOException $e) {
    echo json_encode(["mensagem" => "Erro ao cadastrar turma: " . $e->getMessage()]);
}

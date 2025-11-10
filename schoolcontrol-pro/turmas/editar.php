<?php
header("Content-Type: application/json; charset=utf-8");
require_once "../../config/conexao.php";

$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados || empty($dados["id_turma"]) || empty($dados["nome"]) || empty($dados["ano"])) {
    echo json_encode(["mensagem" => "Dados inválidos."]);
    exit;
}

try {
    // Evita duplicar nome + ano em outra turma
    $verifica = $conn->prepare("SELECT COUNT(*) FROM turmas WHERE nome = ? AND ano = ? AND id_turma != ?");
    $verifica->execute([$dados["nome"], $dados["ano"], $dados["id_turma"]]);
    if ($verifica->fetchColumn() > 0) {
        echo json_encode(["mensagem" => "Já existe outra turma com esse nome e ano."]);
        exit;
    }

    $sql = "UPDATE turmas SET nome = ?, ano = ?, turno = ? WHERE id_turma = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $dados["nome"],
        $dados["ano"],
        $dados["turno"] ?? null,
        $dados["id_turma"]
    ]);

    echo json_encode(["mensagem" => "Turma atualizada com sucesso!"]);
} catch (PDOException $e) {
    echo json_encode(["mensagem" => "Erro ao atualizar turma: " . $e->getMessage()]);
}

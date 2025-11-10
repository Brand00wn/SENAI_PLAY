<?php
header("Content-Type: application/json; charset=utf-8");
require_once "../../config/conexao.php";

$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados || empty($dados["id_turma"])) {
    echo json_encode(["mensagem" => "ID da turma não informado."]);
    exit;
}

$id_turma = $dados["id_turma"];
$disciplinas = $dados["disciplinas"] ?? [];

try {
    $conn->beginTransaction();

    // Remove vínculos antigos
    $delete = $conn->prepare("DELETE FROM turmas_disciplina WHERE id_turma = ?");
    $delete->execute([$id_turma]);

    // Adiciona novos vínculos
    if (!empty($disciplinas)) {
        $insert = $conn->prepare("INSERT INTO turmas_disciplina (id_turma, id_disciplina) VALUES (?, ?)");
        foreach ($disciplinas as $id_disciplina) {
            $insert->execute([$id_turma, $id_disciplina]);
        }
    }

    $conn->commit();
    echo json_encode(["mensagem" => "Disciplinas vinculadas com sucesso!"]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(["mensagem" => "Erro ao vincular disciplinas: " . $e->getMessage()]);
}

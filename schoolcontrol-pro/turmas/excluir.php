<?php
header("Content-Type: application/json; charset=utf-8");
require_once "../../config/conexao.php";

if (!isset($_GET["id_turma"])) {
    echo json_encode(["mensagem" => "ID da turma não informado."]);
    exit;
}

$id_turma = $_GET["id_turma"];

try {
    // Verifica se há matrículas associadas
    $check = $conn->prepare("SELECT COUNT(*) FROM matriculas WHERE id_turma = ?");
    $check->execute([$id_turma]);
    if ($check->fetchColumn() > 0) {
        echo json_encode(["mensagem" => "Não é possível excluir: há matrículas associadas a esta turma."]);
        exit;
    }

    $sql = "DELETE FROM turmas WHERE id_turma = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_turma]);

    echo json_encode(["mensagem" => "Turma excluída com sucesso!"]);
} catch (PDOException $e) {
    echo json_encode(["mensagem" => "Erro ao excluir turma: " . $e->getMessage()]);
}

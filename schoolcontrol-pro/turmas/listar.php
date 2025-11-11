<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

require_once('../config/conexao.php');

try {
    $sql = "SELECT id_turma, nome, ano, turno FROM turmas ORDER BY id_turma DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Erro na consulta: " . $conn->error);
    }

    // Modo 1: pega todos os resultados como array associativo
    $turmas = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'turmas' => $turmas]);

    $result->free();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensagem' => 'Erro ao listar turmas.', 'erro' => $e->getMessage()]);
}

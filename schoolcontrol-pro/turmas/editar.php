<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once('../config/conexao.php'); 

$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'JSON inválido.']);
    exit;
}

$id    = isset($dados['id_turma']) ? (int)$dados['id_turma'] : 0;
$nome  = isset($dados['nome']) ? trim($dados['nome']) : null;
$ano   = isset($dados['ano']) ? (int)$dados['ano'] : null;
$turno = isset($dados['turno']) ? trim($dados['turno']) : null;

if ($id <= 0 || empty($nome) || empty($ano)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'ID, nome e ano são obrigatórios.']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE turmas SET nome = ?, ano = ?, turno = ? WHERE id_turma = ?");
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conn->error);
    }

    $stmt->bind_param("sisi", $nome, $ano, $turno, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'mensagem' => 'Turma atualizada com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'mensagem' => 'Nenhuma alteração realizada ou turma não encontrada.']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'mensagem' => 'Erro ao atualizar turma.',
        'erro' => $e->getMessage()
    ]);
}

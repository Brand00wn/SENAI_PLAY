<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once('../config/conexao.php'); // deve criar $conn = new mysqli(...);

$dados = json_decode(file_get_contents('php://input'), true);
if (!$dados) {
    // fallback se veio via POST tradicional
    $dados = $_POST ?? [];
}

$id = isset($dados['id_turma']) ? (int)$dados['id_turma'] : 0;

// também permite id na URL (?id_turma=)
if ($id <= 0 && isset($_GET['id_turma'])) {
    $id = (int)$_GET['id_turma'];
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM turmas WHERE id_turma = ?");
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conn->error);
    }

    // 'i' = inteiro
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'mensagem' => 'Turma excluída com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'mensagem' => 'Nenhuma turma encontrada com esse ID.']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensagem' => 'Erro ao excluir turma.', 'erro' => $e->getMessage()]);
}

<?php
// api/vincular_disciplinas.php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . 'conexao.php';
$dados = json_decode(file_get_contents('php://input'), true);
if (!$dados) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'JSON inválido.']);
    exit;
}

$id_turma = isset($dados['id_turma']) ? (int)$dados['id_turma'] : 0;
$disciplinas = isset($dados['disciplinas']) && is_array($dados['disciplinas']) ? $dados['disciplinas'] : [];

if ($id_turma <= 0 || empty($disciplinas)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'ID da turma e lista de disciplinas são obrigatórios.']);
    exit;
}

try {
    $conn->beginTransaction();
    // Remove vínculos antigos se necessário (opcional)
    $del = $conn->prepare("DELETE FROM turma_disciplina WHERE id_turma = :id_turma");
    $del->execute([':id_turma' => $id_turma]);

    $ins = $conn->prepare("INSERT INTO turma_disciplina (id_turma, id_disciplina) VALUES (:id_turma, :id_disciplina)");
    foreach ($disciplinas as $id_disc) {
        $id_disc = (int)$id_disc;
        if ($id_disc > 0) {
            $ins->execute([':id_turma' => $id_turma, ':id_disciplina' => $id_disc]);
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'mensagem' => 'Disciplinas vinculadas com sucesso.']);
} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'mensagem' => 'Erro ao vincular disciplinas.']);
}

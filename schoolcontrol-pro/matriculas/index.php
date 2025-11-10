<?php
// matriculas/index.php - API para Matrículas
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/conexao.php';

action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($action === 'list_alunos') {
        $stmt = $conn->query("SELECT id_aluno, nome FROM alunos ORDER BY nome");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($action === 'list_turmas') {
        $stmt = $conn->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($action === 'list_matriculas') {
        $stmt = $conn->query("SELECT m.id_matricula, a.nome AS aluno, t.nome_turma AS turma, m.data_matricula
                               FROM matriculas m
                               JOIN alunos a ON a.id_aluno = m.id_aluno
                               JOIN turmas t ON t.id_turma = m.id_turma");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($action === 'create_matricula' && $method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id_aluno = (int)$input['id_aluno'];
        $id_turma = (int)$input['id_turma'];

        $stmt = $conn->prepare("INSERT INTO matriculas (id_aluno, id_turma, data_matricula) VALUES (?, ?, NOW())");
        $stmt->execute([$id_aluno, $id_turma]);

        echo json_encode(['success' => true, 'id_matricula' => $conn->lastInsertId()]);
        exit;
    }

    if ($action === 'delete_matricula') {
        $id = (int)$_GET['id_matricula'];
        $stmt = $conn->prepare("DELETE FROM matriculas WHERE id_matricula=?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Ação inválida']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
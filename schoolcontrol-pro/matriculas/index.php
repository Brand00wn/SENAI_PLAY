<?php
// API adapted for Matriculas (from professor module)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/conexao.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($action === 'list_alunos') {
        $sql = "SELECT id_aluno, nome FROM alunos ORDER BY nome";
        $res = $conn->query($sql);
        $data = [];
        while ($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    if ($action === 'list_turmas') {
        $sql = "SELECT id_turma, nome FROM turmas ORDER BY nome";
        $res = $conn->query($sql);
        $data = [];
        while ($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    if ($action === 'list_matriculas') {
        $sql = "
            SELECT 
                m.id_matricula,
                a.nome AS aluno,
                t.nome AS turma,
                m.data_matricula
            FROM matriculas m
            JOIN alunos a ON a.id_aluno = m.id_aluno
            JOIN turmas t ON t.id_turma = m.id_turma
            ORDER BY m.data_matricula DESC
        ";
        $res = $conn->query($sql);
        $data = [];
        while ($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    if ($action === 'create_matricula' && $method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $id_aluno = isset($body['id_aluno']) ? (int)$body['id_aluno'] : 0;
        $id_turma = isset($body['id_turma']) ? (int)$body['id_turma'] : 0;
        if (!$id_aluno || !$id_turma) {
            echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO matriculas (id_aluno, id_turma, data_matricula) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $id_aluno, $id_turma);
        $ok = $stmt->execute();
        if ($ok) echo json_encode(['success' => true, 'id_matricula' => $stmt->insert_id]);
        else echo json_encode(['success' => false, 'error' => $stmt->error]);
        exit;
    }

    if ($action === 'delete_matricula') {
        $id = (int)($_GET['id_matricula'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'error' => 'ID inválido']); exit; }
        $stmt = $conn->prepare("DELETE FROM matriculas WHERE id_matricula = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }

    if ($action === 'update_matricula' && $method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $id_matricula = isset($body['id_matricula']) ? (int)$body['id_matricula'] : 0;
        $id_aluno = isset($body['id_aluno']) ? (int)$body['id_aluno'] : 0;
        $id_turma = isset($body['id_turma']) ? (int)$body['id_turma'] : 0;
    
        if (!$id_matricula || !$id_aluno || !$id_turma) {
            echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
            exit;
        }
    
        $stmt = $conn->prepare("UPDATE matriculas SET id_aluno = ?, id_turma = ? WHERE id_matricula = ?");
        $stmt->bind_param("iii", $id_aluno, $id_turma, $id_matricula);
        $ok = $stmt->execute();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
    

    echo json_encode(['success' => false, 'error' => 'Ação inválida']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

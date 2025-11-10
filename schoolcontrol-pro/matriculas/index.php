<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/config/conexao.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        // ========== ALUNOS ==========
        case 'list_alunos':
            $stmt = $conn->query("SELECT id_aluno, nome, email, telefone FROM alunos ORDER BY nome");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        case 'get_aluno':
            $id = (int)($_GET['id_aluno'] ?? 0);
            $stmt = $conn->prepare("SELECT * FROM alunos WHERE id_aluno = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'data' => $stmt->fetch(PDO::FETCH_ASSOC)]);
            break;

        case 'create_aluno':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO alunos (nome, email, telefone) VALUES (?, ?, ?)");
            $stmt->execute([$data['nome'], $data['email'], $data['telefone']]);
            echo json_encode(['success' => true]);
            break;

        case 'update_aluno':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE alunos SET nome=?, email=?, telefone=? WHERE id_aluno=?");
            $stmt->execute([$data['nome'], $data['email'], $data['telefone'], $data['id_aluno']]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_aluno':
            $id = (int)($_GET['id_aluno'] ?? 0);
            $stmt = $conn->prepare("DELETE FROM alunos WHERE id_aluno=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ========== TURMAS ==========
        case 'list_turmas':
            $stmt = $conn->query("SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        // ========== MATRICULAS ==========
        case 'list_matriculas':
            $stmt = $conn->query("
                SELECT m.id_matricula, a.nome AS aluno, t.nome_turma AS turma, m.data_matricula
                FROM matriculas m
                JOIN alunos a ON m.id_aluno = a.id_aluno
                JOIN turmas t ON m.id_turma = t.id_turma
                ORDER BY m.data_matricula DESC
            ");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        case 'create_matricula':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmtA = $conn->prepare("SELECT 1 FROM alunos WHERE id_aluno=?");
            $stmtT = $conn->prepare("SELECT 1 FROM turmas WHERE id_turma=?");
            $stmtA->execute([$data['id_aluno']]);
            $stmtT->execute([$data['id_turma']]);
            if (!$stmtA->fetch() || !$stmtT->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Aluno ou turma inexistente']);
                break;
            }
            $stmt = $conn->prepare("INSERT INTO matriculas (id_aluno, id_turma, data_matricula) VALUES (?, ?, ?)");
            $stmt->execute([$data['id_aluno'], $data['id_turma'], $data['data_matricula']]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_matricula':
            $id = (int)($_GET['id_matricula'] ?? 0);
            $stmt = $conn->prepare("DELETE FROM matriculas WHERE id_matricula=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Ação inválida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

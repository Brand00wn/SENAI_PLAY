<?php
// matriculas/index.php → AGORA É A API DE ALUNOS
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/conexao.php'; // mantém sua conexão existente

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

// lê JSON se for POST
$input = null;
if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
}

try {
    if ($method === 'GET') {
        // listar todos
        if ($action === 'list') {
            $stmt = $conn->prepare("SELECT id, nome, email, telefone, data_nascimento FROM alunos ORDER BY id DESC");
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            exit;
        }
        // obter 1 aluno
        elseif ($action === 'get' && isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT id, nome, email, telefone, data_nascimento FROM alunos WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: new stdClass());
            exit;
        }
    }

    if ($method === 'POST' && isset($input['action'])) {
        $act = $input['action'];
        $data = $input['data'] ?? [];

        if ($act === 'create') {
            $stmt = $conn->prepare("INSERT INTO alunos (nome, email, telefone, data_nascimento) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['nome'] ?? null,
                $data['email'] ?? null,
                $data['telefone'] ?? null,
                $data['data_nascimento'] ?? null
            ]);
            echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
            exit;
        }

        if ($act === 'update') {
            $stmt = $conn->prepare("UPDATE alunos SET nome=?, email=?, telefone=?, data_nascimento=? WHERE id=?");
            $stmt->execute([
                $data['nome'] ?? null,
                $data['email'] ?? null,
                $data['telefone'] ?? null,
                $data['data_nascimento'] ?? null,
                $data['id']
            ]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($act === 'delete') {
            $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
            $stmt->execute([$data['id']]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

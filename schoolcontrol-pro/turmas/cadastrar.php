<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once('../config/conexao.php'); // deve criar $conn = new mysqli(...);

$dados = json_decode(file_get_contents('php://input'), true);
if (!$dados) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'JSON inválido.']);
    exit;
}

$nome = isset($dados['nome']) ? trim($dados['nome']) : '';
$ano = isset($dados['ano']) ? (int)$dados['ano'] : 0;
$turno = isset($dados['turno']) ? trim($dados['turno']) : null;
$disciplinas = isset($dados['disciplinas']) && is_array($dados['disciplinas']) ? $dados['disciplinas'] : [];

if ($nome === '' || $ano <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensagem' => 'Preencha os campos obrigatórios (nome e ano).']);
    exit;
}

try {
    // Inserir turma
    $stmt = $conn->prepare("INSERT INTO turmas (nome, ano, turno) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conn->error);
    }

    // 's' = string, 'i' = inteiro, 's' = string
    $stmt->bind_param("sis", $nome, $ano, $turno);
    $stmt->execute();

    $id_turma = $conn->insert_id;
    $stmt->close();

    // Inserir disciplinas vinculadas, se houver
    if (!empty($disciplinas)) {
        $ins = $conn->prepare("INSERT IGNORE INTO turma_disciplina (id_turma, id_disciplina) VALUES (?, ?)");
        if (!$ins) {
            throw new Exception("Erro ao preparar vínculo: " . $conn->error);
        }

        foreach ($disciplinas as $id_disc) {
            $id_disc = (int)$id_disc;
            if ($id_disc > 0) {
                $ins->bind_param("ii", $id_turma, $id_disc);
                $ins->execute();
            }
        }
        $ins->close();
    }

    echo json_encode(['success' => true, 'mensagem' => 'Turma cadastrada com sucesso.', 'id_turma' => $id_turma]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensagem' => 'Erro ao cadastrar turma.', 'erro' => $e->getMessage()]);
}

$conn->close();

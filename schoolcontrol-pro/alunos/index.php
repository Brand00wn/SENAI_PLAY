<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Tratamento para requisição OPTIONS (pré-flight do CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Importa a conexão
require_once("../config/conexao.php");

$metodo = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

// =====================
// MÉTODO GET - Listar todos os alunos
// =====================
if ($metodo === 'GET') {
    $sql = "SELECT * FROM alunos";
    $res = $conn->query($sql);

    $alunos = [];
    while ($linha = $res->fetch_assoc()) {
        $alunos[] = $linha;
    }

    echo json_encode($alunos);
    exit;
}

// =====================
// MÉTODO POST - Criar novo aluno
// =====================
if ($metodo === 'POST') {
    $nome = $input['nome'] ?? '';
    $email = $input['email'] ?? '';

    $stmt = $conexao->prepare("INSERT INTO aluno (nome, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $nome, $email);

    if ($stmt->execute()) {
        echo json_encode(["id" => $stmt->insert_id, "mensagem" => "Aluno criado com sucesso"]);
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Falha ao criar aluno"]);
    }
    exit;
}

// =====================
// MÉTODO PUT - Atualizar aluno existente
// =====================
if ($metodo === 'PUT') {
    $id = $input['id'] ?? 0;
    $nome = $input['nome'] ?? '';
    $email = $input['email'] ?? '';

    $stmt = $conexao->prepare("UPDATE aluno SET nome = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nome, $email, $id);

    if ($stmt->execute()) {
        echo json_encode(["mensagem" => "Aluno atualizado com sucesso"]);
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Falha ao atualizar alunos"]);
    }
    exit;
}

// =====================
// MÉTODO DELETE - Excluir aluno
// =====================
if ($metodo === 'DELETE') {
    $id = $input['id'] ?? 0;

    $stmt = $conexao->prepare("DELETE FROM aluno WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensagem" => "Aluno deletado com sucesso"]);
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Falha ao deletar aluno"]);
    }
    exit;
}

// =====================
// Caso método não seja suportado
// =====================
http_response_code(405);
echo json_encode(["erro" => "Método não permitido"]);
exit;
?>

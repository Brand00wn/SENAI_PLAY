<?php
error_reporting(E_ALL); // Mudar para E_ALL para depuração, depois pode voltar para 0
ini_set('display_errors', 1);
header("Content-Type: application/json; charset=UTF-8");

// Configurações de Conexão (Mantenha as suas)
$host = "localhost";
$usuario = "root";
$senha = "alunolab";
$banco = "schoolcontrol";

// Conexão com o Banco de Dados
$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    // Retorna erro de conexão em formato JSON
    http_response_code(500);
    die(json_encode(["error" => "Erro na conexão: " . $conn->connect_error]));
}

// Define a ação a ser executada
$acao = $_GET['acao'] ?? '';

// Função para retornar sucesso ou erro
function response($data) {
    echo json_encode($data);
    exit;
}

// Início do Switch para as ações
switch ($acao) {

    case 'listar':
        $result = $conn->query("SELECT * FROM alunos ORDER BY id_aluno DESC");
        $alunos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $alunos[] = $row;
            }
            response($alunos);
        } else {
            http_response_code(500);
            response(["error" => "Erro ao listar: " . $conn->error]);
        }
        break;

    case 'cadastrar':
        // Usa $_POST para dados de formulário
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $data = $_POST['data_nascimento'] ?? '';

        if (!$nome || !$email || !$data) {
            http_response_code(400);
            response(["error" => "Todos os campos são obrigatórios"]);
        }

        // Prepara e executa a inserção
        $stmt = $conn->prepare("INSERT INTO alunos (nome, email, data_nascimento) VALUES (?, ?, ?)");
        if (!$stmt) {
            http_response_code(500);
            response(["error" => "Erro na preparação da query: " . $conn->error]);
        }
        $stmt->bind_param("sss", $nome, $email, $data);
        
        if ($stmt->execute()) {
            response(["success" => true, "message" => "Aluno cadastrado com sucesso!"]);
        } else {
            http_response_code(500);
            response(["error" => "Erro ao cadastrar: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'editar':
        // Usa $_POST para dados de formulário
        $id = $_POST['id'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $data = $_POST['data_nascimento'] ?? '';

        if (!$id || !$nome || !$email || !$data) {
            http_response_code(400);
            response(["error" => "Todos os campos são obrigatórios para edição"]);
        }

        // Prepara e executa a atualização
        $stmt = $conn->prepare("UPDATE alunos SET nome=?, email=?, data_nascimento=? WHERE id_aluno=?");
        if (!$stmt) {
            http_response_code(500);
            response(["error" => "Erro na preparação da query: " . $conn->error]);
        }
        // O 'i' é para o ID que é um inteiro
        $stmt->bind_param("sssi", $nome, $email, $data, $id); 
        
        if ($stmt->execute()) {
            response(["success" => true, "message" => "Aluno editado com sucesso!"]);
        } else {
            http_response_code(500);
            response(["error" => "Erro ao editar: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'excluir':
        // Usa $_GET para o ID na exclusão
        $id = $_GET['id'] ?? '';
        
        if (!$id) {
            http_response_code(400);
            response(["error" => "ID não informado para exclusão"]);
        }
        
        // Prepara e executa a exclusão
        $stmt = $conn->prepare("DELETE FROM alunos WHERE id_aluno=?");
        if (!$stmt) {
            http_response_code(500);
            response(["error" => "Erro na preparação da query: " . $conn->error]);
        }
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            response(["success" => true, "message" => "Aluno excluído com sucesso!" . $id]);
        } else {
            http_response_code(500);
            response(["error" => "Erro ao excluir: " . $stmt->error]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(400);
        response(["error" => "Ação inválida"]);
        break;
}

// A conexão é fechada no final do script, mas a função response() já encerra a execução.
$conn->close();
?>

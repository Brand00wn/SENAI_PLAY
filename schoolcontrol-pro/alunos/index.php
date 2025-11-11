<?php
header("Content-Type: application/json; charset=UTF-8");

// ==== CONFIGURAÇÃO DO BANCO DE DADOS ====
$host = "localhost";
$dbname = "crud_alunos";  // nome do banco de dados
$user = "root";           // seu usuário MySQL
$pass = "";               // senha do seu MySQL (ex: 'root' no XAMPP)

// ==== CONEXÃO COM O BANCO ====
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(["error" => "Erro ao conectar ao banco: " . $e->getMessage()]);
    exit;
}

// ==== PEGA A AÇÃO DA REQUISIÇÃO ====
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    // ============================================================
    // LISTAR ALUNOS
    // ============================================================
    case 'listar':
        try {
            $stmt = $pdo->query("SELECT * FROM alunos ORDER BY id DESC");
            $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($alunos);
        } catch (Exception $e) {
            echo json_encode(["error" => "Erro ao listar: " . $e->getMessage()]);
        }
        break;

    // ============================================================
    // CADASTRAR ALUNO
    // ============================================================
    case 'cadastrar':
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $data_nascimento = $_POST['data_nascimento'] ?? '';

        if (!$nome || !$email || !$data_nascimento) {
            echo json_encode(["error" => "Todos os campos são obrigatórios."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO alunos (nome, email, data_nascimento) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $data_nascimento]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["error" => "Erro ao cadastrar: " . $e->getMessage()]);
        }
        break;

    // ============================================================
    // EDITAR ALUNO
    // ============================================================
    case 'editar':
        $id = $_POST['id'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $data_nascimento = $_POST['data_nascimento'] ?? '';

        if (!$id || !$nome || !$email || !$data_nascimento) {
            echo json_encode(["error" => "Todos os campos são obrigatórios."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE alunos SET nome=?, email=?, data_nascimento=? WHERE id=?");
            $stmt->execute([$nome, $email, $data_nascimento, $id]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["error" => "Erro ao editar: " . $e->getMessage()]);
        }
        break;

    // ============================================================
    // EXCLUIR ALUNO
    // ============================================================
    case 'excluir':
        $id = $_GET['id'] ?? '';
        if (!$id) {
            echo json_encode(["error" => "ID não informado."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM alunos WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["error" => "Erro ao excluir: " . $e->getMessage()]);
        }
        break;

    // ============================================================
    // AÇÃO INVÁLIDA
    // ============================================================
    default:
        echo json_encode(["error" => "Ação inválida."]);
        break;
}

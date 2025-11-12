<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$pass = "alunolab";
$dbname = "schoolcontrol-pro"; 
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erro de conexão: " . $conn->connect_error]));
}

// PUT = atualizar
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["id"] ?? "";
    $nome = $data["nome"] ?? "";
    $email = $data["email"] ?? "";
    $tel = $data["telefone"] ?? "";

    $sql = "UPDATE professor SET nome=?, email=?, telefone=? WHERE id_professor=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $email, $tel, $id);
    $stmt->execute();

    echo json_encode(["message" => "Professor atualizado com sucesso."]);
    exit;
}

// POST = criar novo
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $nome = $data["nome"] ?? "";
    $email = $data["email"] ?? "";
    $tel = $data["telefone"] ?? "";

    $sql = "INSERT INTO professor (nome, email, telefone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome, $email, $tel);
    $stmt->execute();

    echo json_encode(["message" => "Professor inserido com sucesso."]);
    exit;
}

// DELETE = excluir
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["id"] ?? null;

    if ($id) {
        $conn->query("DELETE FROM professor WHERE id_professor=$id");
        echo json_encode(["message" => "Professor excluído com sucesso."]);
    } else {
        echo json_encode(["error" => "ID não fornecido."]);
    }
    exit;
}

// GET = listar
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $conn->query("SELECT * FROM professor ORDER BY id_professor DESC");
    $professores = [];

    while ($row = $result->fetch_assoc()) {
        $professores[] = $row;
    }

    echo json_encode($professores);
    exit;
}

$conn->close();
?>

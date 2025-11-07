<?php 

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "schoolcontropro";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Definir o tipo de resposta como JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_METHOD"] === "PUT") {
    // Receber o corpo da requisição (em JSON)
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data["id"] ?? null;
    $nome = $data["nome"] ?? "";
    $dis = $data["disciplina"] ?? "";
    $email = $data["email"] ?? "";
    $tel = $data["telefone"] ?? "";

    if ($id) {
        // Atualizar professor
        $sql = "UPDATE professores SET nome=?, disciplina=?, email=?, telefone=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $dis, $email, $tel, $id);
        $stmt->execute();
    } else {
        // Inserir novo professor
        $sql = "INSERT INTO professores (nome, disciplina, email, telefone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $dis, $email, $tel);
        $stmt->execute();
    }

    echo json_encode(["message" => "Operação realizada com sucesso."]);
    exit;
}

// Excluir professor via DELETE
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // Receber o corpo da requisição (em JSON)
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["id"] ?? null;

    if ($id) {
        $conn->query("DELETE FROM professores WHERE id=$id");
        echo json_encode(["message" => "Professor excluído com sucesso."]);
    } else {
        echo json_encode(["error" => "ID não fornecido."]);
    }

    exit;
}

// Carregar todos os professores (GET)
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $conn->query("SELECT * FROM professores ORDER BY id DESC");
    $professores = [];
    
    while ($row = $result->fetch_assoc()) {
        $professores[] = $row;
    }

    echo json_encode($professores);
    exit;
}

$conn->close();

?>

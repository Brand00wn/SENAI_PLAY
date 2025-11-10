<?php
header("Content-Type: application/json; charset=utf-8");
require_once "../../config/conexao.php";

try {
    $sql = "SELECT * FROM turmas ORDER BY nome ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($turmas ?: []);
} catch (PDOException $e) {
    echo json_encode(["erro" => "Erro ao listar turmas: " . $e->getMessage()]);
}

<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "schoolcontrol";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["erro" => "Erro na conexão: " . $conn->connect_error]);
  exit;
}
?>

<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "flight_reservation";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "error"=>"DB connection failed"]);
  exit;
}

$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
  http_response_code(401);
  echo json_encode(["ok"=>false, "error"=>"NO_SESSION"]);
  exit;
}

$flight_id = intval($_POST["flight_id"] ?? 0);
if ($flight_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>"BAD_FLIGHT_ID"]);
  exit;
}

// Evitar duplicados (opcional pero recomendado)
$check = $conn->prepare("SELECT id FROM reservations WHERE user_id=? AND flight_id=? LIMIT 1");
$check->bind_param("ii", $user_id, $flight_id);
$check->execute();
$exists = $check->get_result()->fetch_assoc();
if ($exists) {
  echo json_encode(["ok"=>true, "msg"=>"Ya estaba reservada"]);
  exit;
}

$stmt = $conn->prepare("INSERT INTO reservations (user_id, flight_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $flight_id);

if ($stmt->execute()) {
  echo json_encode(["ok"=>true, "msg"=>"Reserva creada"]);
} else {
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>$conn->error]);
}

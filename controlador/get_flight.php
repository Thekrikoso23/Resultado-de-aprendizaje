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

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) {
  http_response_code(401);
  echo json_encode(["ok"=>false, "error"=>"NO_SESSION"]);
  exit;
}

$origin = trim($_GET["origin"] ?? "");
$destination = trim($_GET["destination"] ?? "");
$date = trim($_GET["date"] ?? "");

// Query base
$sql = "SELECT id, flight_code, origin, destination, flight_date, price, airline FROM flights WHERE 1=1";
$params = [];
$types = "";

// Filtros opcionales
if ($origin !== "") {
  $sql .= " AND origin LIKE ?";
  $params[] = "%".$origin."%";
  $types .= "s";
}
if ($destination !== "") {
  $sql .= " AND destination LIKE ?";
  $params[] = "%".$destination."%";
  $types .= "s";
}
if ($date !== "") {
  // date debe venir YYYY-MM-DD
  $sql .= " AND flight_date = ?";
  $params[] = $date;
  $types .= "s";
}

$sql .= " ORDER BY flight_date ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["ok"=>false, "error"=>"prepare_failed", "detail"=>$conn->error]);
  exit;
}

if (count($params) > 0) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($row = $res->fetch_assoc()) {
  $rows[] = $row;
}

echo json_encode(["ok"=>true, "flights"=>$rows]);

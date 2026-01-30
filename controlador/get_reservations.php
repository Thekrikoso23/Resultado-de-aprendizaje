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

$sql = "
SELECT 
  r.id AS reservation_id,
  r.reserved_at,
  f.flight_code,
  f.origin,
  f.destination,
  f.flight_date,
  f.price,
  f.airline
FROM reservations r
JOIN flights f ON f.id = r.flight_id
WHERE r.user_id = ?
ORDER BY r.reserved_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($row = $res->fetch_assoc()) {
  $rows[] = $row;
}

echo json_encode(["ok"=>true, "reservations"=>$rows]);

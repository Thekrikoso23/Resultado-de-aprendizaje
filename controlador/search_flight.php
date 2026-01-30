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
  echo json_encode(["ok"=>false, "error"=>"DB connection failed", "detail"=>$conn->connect_error]);
  exit;
}

// (Opcional) si quieres obligar login para buscar:
if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false, "error"=>"NO_SESSION"]);
  exit;
}

$origin = trim($_GET["origin"] ?? "");
$destination = trim($_GET["destination"] ?? "");
$date = trim($_GET["date"] ?? "");

// Si no mandan filtros, devuelve todo
$sql = "SELECT id, flight_code, origin, destination, flight_date, price, airline
        FROM flights
        WHERE ( ? = '' OR origin LIKE CONCAT('%', ?, '%') )
          AND ( ? = '' OR destination LIKE CONCAT('%', ?, '%') )
          AND ( ? = '' OR flight_date = ? )
        ORDER BY flight_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $origin, $origin, $destination, $destination, $date, $date);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($row = $res->fetch_assoc()) $rows[] = $row;

echo json_encode(["ok"=>true, "flights"=>$rows]);

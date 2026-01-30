<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flight_reservation";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM Reservations WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

header("Content-Type: application/json");
echo json_encode($reservations);

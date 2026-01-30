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
    echo "Not logged in";
    exit;
}

$user_id = $_SESSION['user_id'];
$flight_id = intval($_POST['flight_id'] ?? 0);

$stmt = $conn->prepare("INSERT INTO Reservations (user_id, flight_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $flight_id);

if ($stmt->execute()) {
    echo "Reservation successful";
} else {
    http_response_code(400);
    echo "Error: " . $conn->error;
}

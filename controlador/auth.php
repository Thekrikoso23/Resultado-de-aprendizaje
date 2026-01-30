<?php
session_start();

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "flight_reservation";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

header("Content-Type: application/json; charset=utf-8");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "DB connection failed"]);
    exit;
}

$action = $_POST["action"] ?? "";

// =====================
// REGISTRO
// =====================
if ($action === "register") {
    $name  = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $plain = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $plain === "") {
        echo json_encode(["ok" => false, "error" => "missing"]);
        exit;
    }

    $hash = password_hash($plain, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "prepare_failed"]);
        exit;
    }

    $stmt->bind_param("sss", $name, $email, $hash);

    if ($stmt->execute()) {
        echo json_encode(["ok" => true, "msg" => "Registro exitoso"]);
        exit;
    } else {
        // Puede ser correo duplicado u otro error
        echo json_encode(["ok" => false, "error" => "db"]);
        exit;
    }
}

// =====================
// LOGIN
// =====================
if ($action === "login") {
    $email = trim($_POST["email"] ?? "");
    $plain = $_POST["password"] ?? "";

    if ($email === "" || $plain === "") {
        echo json_encode(["ok" => false, "error" => "missing"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "prepare_failed"]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if (password_verify($plain, $row["password"])) {
            // ✅ Guardamos sesión
            $_SESSION["user_id"] = (int)$row["id"];
            $_SESSION["user_name"] = $row["name"];

            echo json_encode(["ok" => true, "msg" => "Login exitoso"]);
            exit;
        }
    }

    echo json_encode(["ok" => false, "error" => "bad"]);
    exit;
}

// =====================
// ACCIÓN NO VÁLIDA
// =====================
echo json_encode(["ok" => false, "error" => "action"]);
exit;

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "flight_reservation";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header("Content-Type: application/json");

$action = $_POST['action'] ?? "";

// =====================
// REGISTRO
// =====================
if ($action == "register") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO Users (name, email, password) VALUES ('$name', '$email', '$pass')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["ok" => true, "msg" => "Registro exitoso"]);
    } else {
        echo json_encode(["ok" => false, "error" => $conn->error]);
    }
    exit;
}

// =====================
// LOGIN
// =====================
if ($action == "login") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT id, name, email, password FROM Users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($pass, $row['password'])) {
            echo json_encode([
                "ok" => true,
                "user_id" => $row["id"],
                "name" => $row["name"],
                "email" => $row["email"]
            ]);
        } else {
            echo json_encode(["ok" => false, "error" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["ok" => false, "error" => "Usuario no existe"]);
    }
    exit;
}

echo json_encode(["ok" => false, "error" => "Acción no válida"]);

$conn->close();
?>

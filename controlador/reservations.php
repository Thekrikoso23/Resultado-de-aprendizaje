<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Reservations</title>
</head>
<body>
  <h1>Reservaciones</h1>
  <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></p>

  <div id="list"></div>

  <script>
    // Aquí luego llamas a manage_reservations.php
  </script>
</body>
</html>

<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

echo json_encode([
  "ok" => isset($_SESSION["user_id"])
]);

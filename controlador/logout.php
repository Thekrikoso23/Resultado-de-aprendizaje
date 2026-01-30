<?php
session_start();
session_destroy();

// Regresa al login (HTML)
header("Location: ../modelo/login.html");
exit;

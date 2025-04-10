<?php
session_start();
if (!isset($_SESSION['username'])) {
    die("Error: No hay una sesión iniciada.");
}
echo "Sesión iniciada correctamente. Usuario: " . $_SESSION['username'] . ", Rol: " . $_SESSION['role'];
?>

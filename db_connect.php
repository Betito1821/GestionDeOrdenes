<?php
$servername = "localhost";
$username = "root"; // Cambia a tu usuario correcto
$password = ""; // Si usas XAMPP, normalmente no hay contraseña
$dbname = "gestion_ordenes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos!";
}
?>

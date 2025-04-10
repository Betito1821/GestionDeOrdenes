<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'maquetado') {
    header("Location: Proyecto.html");
    exit();
}

$orden_id = $_POST['orden_id'];
$maquetador_id = $_SESSION['user_id'];

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'gestion_ordenes');

// Verificar si la orden ya está en proceso
$result = $conn->query("SELECT estado FROM ordenes WHERE id = $orden_id");
$row = $result->fetch_assoc();
if ($row['estado'] === 'en_proceso') {
    echo "La orden ya está en proceso.";
    exit();
}

// Actualizar el estado de la orden
$sql = "UPDATE ordenes SET estado = 'en_proceso', diseñador_asignado = $maquetador_id WHERE id = $orden_id";
$conn->query($sql);

header("Location: maquetado.html");
?>
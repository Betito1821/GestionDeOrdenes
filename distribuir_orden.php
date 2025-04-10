<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'retrabajo') {
    header("Location: Proyecto.html");
    exit();
}

$orden_id = $_POST['orden_id'];
$area = $_POST['area'];

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'gestion_ordenes');

// Actualizar el área de la orden
$sql = "UPDATE ordenes SET area_actual = '$area', estado = 'no_asignado' WHERE id = $orden_id";
$conn->query($sql);

header("Location: retrabajo.html");
?>
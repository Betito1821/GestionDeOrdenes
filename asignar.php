<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$orden = htmlspecialchars($_POST['orden']);
$designer = htmlspecialchars($_POST['designer']);

// Aquí puedes agregar la lógica para guardar la asignación en una base de datos o archivo
// Por ejemplo, guardar en un archivo de texto
$file = fopen("asignaciones.txt", "a");
if ($file) {
    fwrite($file, "Orden: $orden, Diseñador: $designer\n");
    fclose($file);
    echo "La orden $orden ha sido asignada a $designer.";
} else {
    echo "Error al abrir el archivo de asignaciones.";
}
?>
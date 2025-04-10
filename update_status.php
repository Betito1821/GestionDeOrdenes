<?php
session_start();
include 'db_connect.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Validar datos y procesar la actualización
    if ($order_id > 0 && in_array($status, ['concluida', 'en-proceso', 'no-terminado', 'no-asignado'])) {
        $stmt = $conn->prepare("UPDATE ordenes SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Estado actualizado correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar el estado.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Datos inválidos proporcionados.";
    }

    // Redirigir a impresion.php
    header("Location: impresion.php");
    exit();
}
$conn->close();
?>

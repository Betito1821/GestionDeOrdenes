<?php
session_start(); // Inicia sesión correctamente

// **Validación de Sesión**
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'maquetado') {
    die("<p style='color: red;'>Error: No tienes permisos para realizar esta acción.</p>");
}

// **Conexión a la Base de Datos**
include 'db_connect.php';
if ($conn->connect_error) {
    die("<p style='color: red;'>Conexión fallida: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Verificar que los Datos del Formulario sean Enviados**
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // **Sanitizar y Validar los Datos**
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $decision = isset($_POST['decision']) ? trim($_POST['decision']) : '';
    $comments = isset($_POST['comments']) ? trim($conn->real_escape_string($_POST['comments'])) : '';

    // **Validación de Parámetros**
    if ($order_id > 0 && in_array($decision, ['bien', 'mal'])) {
        if ($decision === 'bien') {
            // **Aprobar la Orden y Cambiar el Estado a 'impresion'**
            $stmt = $conn->prepare("UPDATE ordenes SET estado = 'impresion' WHERE id = ?");
            $stmt->bind_param("i", $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Orden aprobada y enviada al área de impresión.";
            } else {
                $_SESSION['error'] = "Error al enviar la orden a impresión: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            }
        } elseif ($decision === 'mal') {
            // **Validar Comentarios para Retrabajo**
            if (empty($comments)) {
                $_SESSION['error'] = "Por favor, proporciona comentarios para el retrabajo.";
                header("Location: maquetado.php");
                exit();
            }

            // **Rechazar la Orden y Cambiar el Estado a 'retrabajo'**
            $stmt = $conn->prepare("UPDATE ordenes SET estado = 'retrabajo', comentarios = ? WHERE id = ?");
            $stmt->bind_param("si", $comments, $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Orden rechazada y enviada a retrabajo con comentarios.";
            } else {
                $_SESSION['error'] = "Error al enviar la orden a retrabajo: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            }
        }

        // **Cerrar el Statement**
        $stmt->close();
    } else {
        $_SESSION['error'] = "Datos inválidos proporcionados o decisión incorrecta.";
    }
} else {
    $_SESSION['error'] = "No se recibieron datos del formulario.";
}

// **Redirigir al Área de Maquetado**
$conn->close();
header("Location: maquetado.php");
exit();
?>

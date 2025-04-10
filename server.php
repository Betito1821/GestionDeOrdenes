<?php
session_start(); // Inicia sesión al principio del archivo

// **Conexión a la base de datos**
$conn = new mysqli('localhost', 'root', '', 'gestion_ordenes');
if ($conn->connect_error) {
    die("<p style='color: red;'>Conexión fallida: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Inicio de Sesión (Login)**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Verificar usuario y contraseña
    $stmt = $conn->prepare("SELECT username, role FROM usuarios WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirigir según el rol del usuario
        switch ($_SESSION['role']) {
            case 'diseno':
                header("Location: diseno.php");
                break;
            case 'maquetado':
                header("Location: maquetado.php");
                break;
            case 'impresion':
                header("Location: impresion.php");
                break;
            default:
                $_SESSION['error'] = "Rol desconocido. Contacta al administrador.";
                header("Location: index.html");
        }
        exit();
    } else {
        // Usuario o contraseña incorrectos
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: index.html");
        exit();
    }
    $stmt->close();
}

// **Tomar una Orden (Rol: Diseño)**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['take_order'])) {
    // Validación de sesión y rol
    if (!isset($_SESSION['usuario'])) {
        die("<p style='color: red;'>Error: No hay usuario en la sesión.</p>");
    }
    if ($_SESSION['role'] !== 'diseno') {
        die("<p style='color: red;'>Error: Permiso denegado. Rol actual: " . htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') . "</p>");
    }

    // Capturar datos del formulario
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $designer_id = filter_var($_POST['designer_id'], FILTER_VALIDATE_INT);

    if ($order_id && $designer_id) {
        // Actualizar el estado de la orden a 'en-proceso'
        $stmt = $conn->prepare("UPDATE ordenes SET estado = 'en-proceso', designer_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $designer_id, $order_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Orden asignada correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar la orden: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Datos inválidos proporcionados.";
    }

    // Redirigir de vuelta a diseno.php para actualizar la lista
    header("Location: diseno.php");
    exit();
}

// **Manejar Decisiones en Maquetado (Rol: Maquetado)**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_order'])) {
    if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'maquetado') {
        die("<p style='color: red;'>Error: No tienes permisos para realizar esta acción.</p>");
    }

    // Capturar datos del formulario
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $decision = isset($_POST['decision']) ? trim($_POST['decision']) : '';
    $comments = isset($_POST['comments']) ? trim($conn->real_escape_string($_POST['comments'])) : '';

    if ($order_id > 0 && in_array($decision, ['bien', 'mal'])) {
        if ($decision === 'bien') {
            // Aprobar orden: enviar a impresión
            $stmt = $conn->prepare("UPDATE ordenes SET estado = 'impresion' WHERE id = ?");
            $stmt->bind_param("i", $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Orden enviada a impresión.";
            } else {
                $_SESSION['error'] = "Error al enviar la orden a impresión: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            }
        } elseif ($decision === 'mal') {
            // Rechazar orden: enviar a retrabajo
            if (empty($comments)) {
                $_SESSION['error'] = "Por favor, proporciona comentarios para el retrabajo.";
                header("Location: maquetado.php");
                exit();
            }

            $stmt = $conn->prepare("UPDATE ordenes SET estado = 'retrabajo', comentarios = ? WHERE id = ?");
            $stmt->bind_param("si", $comments, $order_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Orden enviada a retrabajo con comentarios.";
            } else {
                $_SESSION['error'] = "Error al enviar la orden a retrabajo: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            }
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Datos inválidos proporcionados.";
    }

    // Redirigir al área de maquetado
    header("Location: maquetado.php");
    exit();
}

// **Cerrar Sesión**
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}

// **Cerrar Conexión**
$conn->close();
?>


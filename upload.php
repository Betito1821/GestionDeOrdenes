<?php
session_start();

// Verificación de permisos
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'planeacion') {
    header("Location: login.html?unauthorized=1");
    exit();
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prioridad = isset($_POST['prioridad']) ? 1 : 0;
    $designer_id = isset($_POST['designer']) ? intval($_POST['designer']) : 0;
    $fecha_subida = date('Y-m-d H:i:s');

    if ($designer_id === 0) {
        echo "<p>Error: Debes asignar un diseñador válido.</p>";
        echo "<p><a href='planeacion.html'>Volver</a></p>";
        exit();
    }

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_paths = [];

    if (isset($_FILES['file']) && !empty($_FILES['file']['name'][0])) {
        foreach ($_FILES['file']['name'] as $key => $name) {
            $file_type = mime_content_type($_FILES['file']['tmp_name'][$key]);
            $allowed_types = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                'application/vnd.ms-excel.sheet.binary.macroEnabled.12',            
                'application/vnd.ms-excel.sheet.macroEnabled.12'
            ];
            if (!in_array($file_type, $allowed_types)) {
                echo "<p>Tipo de archivo no permitido: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</p>";
                continue;
            }

            $sanitized_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($name));
            $target_file = $target_dir . $sanitized_name;

            if (move_uploaded_file($_FILES['file']['tmp_name'][$key], $target_file)) {
                $file_paths[] = $target_file;
            } else {
                echo "<p>Error al subir el archivo: " . htmlspecialchars($sanitized_name, ENT_QUOTES, 'UTF-8') . "</p>";
            }
        }
    } else {
        echo "<p>No se recibieron archivos.</p>";
        echo "<p><a href='planeacion.html'>Volver</a></p>";
        exit();
    }

    if (empty($file_paths)) {
        echo "<p>No se subió ningún archivo correctamente.</p>";
        echo "<p><a href='planeacion.html'>Volver</a></p>";
        exit();
    }

    $order_number = uniqid('ORD-');
    $estado = 'no-asignado';
    $file_paths_json = json_encode($file_paths);

    $stmt = $conn->prepare("INSERT INTO ordenes (order_number, prioridad, designer_id, file_paths, fecha_subida, estado) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "<p>Error en la consulta: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><a href='planeacion.html'>Volver</a></p>";
        exit();
    }

    $stmt->bind_param("siisss", $order_number, $prioridad, $designer_id, $file_paths_json, $fecha_subida, $estado);

    if ($stmt->execute()) {
        echo "<h2>Orden creada con éxito</h2>";
        echo "<p>Número de orden: " . htmlspecialchars($order_number, ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><a href='planeacion.html'>Volver</a></p>";
    } else {
        echo "<p>Error al registrar la orden.</p>";
        echo "<p><a href='planeacion.html'>Volver</a></p>";
    }

    $stmt->close();
}

$conn->close();
?>

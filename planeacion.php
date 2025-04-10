<?php
session_start(); // Inicia sesión correctamente

// **Validar sesión y rol del usuario**
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'planeacion') {
    header("Location: login.html?unauthorized=1");
    exit();
}

// **Mensaje para el usuario**
$message = "";

// **Verificar si se envió el formulario**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    include 'db_connect.php'; // Conexión a la base de datos

    // **Obtener datos del formulario**
    $prioridad = isset($_POST['prioridad']) ? 1 : 0; // 1 si es prioridad, 0 si no
    $fileName = $_FILES['file']['name'][0];
    $fileTmpPath = $_FILES['file']['tmp_name'][0];

    // **Validar el archivo**
    $allowedExtensions = ['xlsx', 'xlsb', 'xlsm'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    if (in_array($fileExtension, $allowedExtensions)) {
        $uploadPath = 'uploads/' . basename($fileName);

        // **Mover el archivo al directorio `uploads/`**
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            // **Generar un valor único para order_number**
            $orderNumber = uniqid('ORD-'); // Genera un número único para la orden

            // **Guardar la orden en la base de datos**
            $stmt = $conn->prepare("INSERT INTO ordenes (order_number, nombre_archivo, prioridad, estado, fecha_subida) VALUES (?, ?, ?, 'no-asignado', NOW())");
            $stmt->bind_param("ssi", $orderNumber, $fileName, $prioridad);

            if ($stmt->execute()) {
                $message = "Archivo subido y orden registrada con éxito.";
            } else {
                $message = "Error al registrar la orden: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8');
            }

            $stmt->close();
        } else {
            $message = "Error al subir el archivo.";
        }
    } else {
        $message = "Formato de archivo no permitido. Solo se aceptan: xlsx, xlsb, xlsm.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Planeación</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        header {
            background-color: #007BFF;
            color: #fff;
            padding: 15px;
        }
        header h1 {
            margin: 0;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin-right: 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        main {
            padding: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="file"], input[type="checkbox"], button {
            margin-bottom: 15px;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Área de Planeación</h1>
        <nav>
            <ul>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Subir Archivos</h2>
        <?php if ($message): ?>
            <p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form action="planeacion.php" method="post" enctype="multipart/form-data">
            <label for="file">Subir archivo (xlsx, xlsb, xlsm):</label>
            <input type="file" id="file" name="file[]" accept=".xlsx,.xlsb,.xlsm" required>

            <label for="prioridad">Marcar como Prioridad:</label>
            <input type="checkbox" id="prioridad" name="prioridad">

            <button type="submit">Subir Archivo</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Proyecto Gestión de Órdenes</p>
    </footer>
</body>
</html>

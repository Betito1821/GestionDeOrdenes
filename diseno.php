<?php
session_start(); // Inicia la sesión correctamente

// **Validación de Sesión**
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role'])) {
    die("<p style='color: red;'>Error: No hay una sesión iniciada o permisos insuficientes.</p>");
}
if ($_SESSION['role'] !== 'diseno') {
    die("<p style='color: red;'>Error: Permiso denegado. Tu rol es: " . htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Conexión a la Base de Datos**
include 'db_connect.php';
if ($conn->connect_error) {
    die("<p style='color: red;'>Conexión fallida: " . $conn->connect_error . "</p>");
}

// **Consulta para obtener las órdenes pendientes**
$query = "SELECT id, order_number, prioridad, fecha_subida, estado 
          FROM ordenes 
          WHERE estado NOT IN ('concluida', 'en-proceso', 'impresion', 'retrabajo')";
$result = $conn->query($query);

if (!$result) {
    die("<p style='color: red;'>Error en la consulta SQL: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Consulta para obtener los diseñadores**
$designers = $conn->query("SELECT id, username FROM usuarios WHERE role = 'diseno'");
if (!$designers) {
    die("<p style='color: red;'>Error en la consulta SQL: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "</p>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Diseño</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f7f7f7;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Área de Diseño</h1>
        <nav>
            <ul>
                <li><a href="server.php?logout=1">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <!-- Mostrar mensajes de éxito o error -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
            unset($_SESSION['error']);
        }
        ?>

        <h2>Órdenes Pendientes</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Número de Orden</th>
                        <th>Prioridad</th>
                        <th>Fecha de Subida</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $row['prioridad'] ? 'Alta' : 'Normal' ?></td>
                            <td><?= htmlspecialchars($row['fecha_subida'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form action="server.php" method="post">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <select name="designer_id" required>
                                        <option value="" disabled selected>Seleccionar Diseñador</option>
                                        <?php while ($designer = $designers->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($designer['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($designer['username'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" name="take_order">Tomar Orden</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay órdenes pendientes para revisar.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Área de Diseño</p>
    </
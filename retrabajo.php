<?php
session_start(); // Inicia sesión correctamente

// **Validación de la Sesión**
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role'])) {
    die("<p style='color: red;'>Error: No hay una sesión iniciada o permisos insuficientes.</p>");
}
if ($_SESSION['role'] !== 'retrabajo') {
    die("<p style='color: red;'>Error: Permiso denegado. Tu rol es: " . htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Conexión a la Base de Datos**
include 'db_connect.php';
if ($conn->connect_error) {
    die("<p style='color: red;'>Conexión fallida: " . $conn->connect_error . "</p>");
}

// **Consulta para obtener órdenes con estado 'retrabajo'**
$query = "SELECT id, order_number, comentarios, estado 
          FROM ordenes 
          WHERE estado = 'retrabajo'";
$result = $conn->query($query);

if (!$result) {
    die("<p style='color: red;'>Error en la consulta SQL: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "</p>");
}

// Depuración: Mensaje en caso de que no existan órdenes con estado 'retrabajo'
if ($result->num_rows === 0) {
    echo "<p style='color: orange;'>Depuración: No se encontraron órdenes con estado 'retrabajo'.</p>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Retrabajo</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-retrabajo {
            background-color: orange;
        }
        .table-header {
            background-color: #f2f2f2;
            font-weight: bold;
        }
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
    </style>
</head>
<body>
    <header>
        <h1>Área de Retrabajo</h1>
        <nav>
            <ul>
                <li><a href="retrabajo.php">Inicio</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Órdenes para Retrabajo</h2>
        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <section>
            <?php if ($result->num_rows > 0): ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead class="table-header">
                        <tr>
                            <th>ID</th>
                            <th>Número de Orden</th>
                            <th>Comentarios</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['comentarios'], ENT_QUOTES, 'UTF-8') ?: 'Sin Comentarios' ?></td>
                                <td>
                                    <span class="status-indicator status-retrabajo"></span> <?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay órdenes pendientes de retrabajo enviadas desde maquetado.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Proyecto</p>
    </footer>
</body>
</html>
<?php
$conn->close(); // Cierra la conexión correctamente
?>

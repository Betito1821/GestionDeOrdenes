<?php
session_start(); // Inicia sesión correctamente

// **Validación de la Sesión**
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role'])) {
    die("<p style='color: red;'>Error: No hay una sesión iniciada o permisos insuficientes.</p>");
}
if ($_SESSION['role'] !== 'impresion') {
    die("<p style='color: red;'>Error: Permiso denegado. Tu rol es: " . htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Conexión a la Base de Datos**
include 'db_connect.php';
if ($conn->connect_error) {
    die("<p style='color: red;'>Conexión fallida: " . $conn->connect_error . "</p>");
}

// **Consulta para obtener órdenes en estado 'impresion'**
$query = "SELECT o.id, o.order_number, o.estado, u.username AS designer, o.comentarios
          FROM ordenes o
          LEFT JOIN usuarios u ON o.designer_id = u.id
          WHERE o.estado = 'impresion'";
$result = $conn->query($query);

if (!$result) {
    die("<p style='color: red;'>Error en la consulta SQL: " . $conn->error . "</p>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Impresión</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-impresion {
            background-color: yellow;
        }
    </style>
</head>
<body>
    <header>
        <h1>Área de Impresión</h1>
    </header>
    <nav>
        <ul>
            <li><a href="impresion.php">Inicio</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <main>
        <h2>Órdenes Pendientes de Impresión</h2>
        <section>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Número de Orden</th>
                            <th>Diseñador Asignado</th>
                            <th>Comentarios</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['designer'] ?: 'No Asignado', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['comentarios'], ENT_QUOTES, 'UTF-8') ?: 'Sin Comentarios' ?></td>
                                <td>
                                    <span class="status-indicator status-impresion"></span> <?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay órdenes pendientes de impresión.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Proyecto</p>
    </footer>
</body>
</html>
<?php
$conn->close();
?>

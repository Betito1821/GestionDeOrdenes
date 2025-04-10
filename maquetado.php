<?php
session_start(); // Inicia sesión correctamente

// **Validación de la Sesión**
if (!isset($_SESSION['usuario']) || !isset($_SESSION['role'])) {
    die("<p style='color: red;'>Error: No hay una sesión iniciada o permisos insuficientes.</p>");
}
if ($_SESSION['role'] !== 'maquetado') {
    die("<p style='color: red;'>Error: Permiso denegado. Tu rol es: " . htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') . "</p>");
}

// **Conexión a la Base de Datos**
include 'db_connect.php';
if ($conn->connect_error) {
    die("<p style='color: red;'>Conexión fallida: " . $conn->connect_error . "</p>");
}

// **Consulta para obtener órdenes en estado 'en-proceso'**
$query = "SELECT o.id, o.order_number, o.estado, o.prioridad, o.fecha_subida, 
                 CASE 
                     WHEN u.username IS NOT NULL THEN u.username 
                     ELSE 'No Asignado'
                 END AS designer 
          FROM ordenes o 
          LEFT JOIN usuarios u ON o.designer_id = u.id 
          WHERE o.estado = 'en-proceso'";
$result = $conn->query($query);

if (!$result) {
    die("<p style='color: red;'>Error en la consulta SQL: " . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "</p>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Maquetado</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Área de Maquetado</h1>
        <nav>
            <ul>
                <li><a href="maquetado.php">Inicio</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Órdenes Pendientes de Revisión</h2>
        <!-- Mostrar mensajes de éxito o error -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p style='color: green; font-weight: bold;'>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo "<p style='color: red; font-weight: bold;'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
            unset($_SESSION['error']);
        }
        ?>
        <section>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Número de Orden</th>
                            <th>Diseñador Asignado</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha de Subida</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['designer'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="<?= $row['prioridad'] ? 'prioridad-alta' : 'prioridad-normal' ?>">
                                    <?= $row['prioridad'] ? 'Alta' : 'Normal' ?>
                                </td>
                                <td><?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['fecha_subida'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <form action="handle_order.php" method="post">
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <select name="decision" required>
                                            <option value="" disabled selected>Seleccionar Acción</option>
                                            <option value="bien">Aprobada (Enviar a Impresión)</option>
                                            <option value="mal">Rechazada (Enviar a Retrabajo)</option>
                                        </select>
                                        <textarea name="comments" placeholder="Comentarios para retrabajo (obligatorio si rechazada)" rows="2"></textarea>
                                        <button type="submit">Procesar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay órdenes pendientes de revisión.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Proyecto Gestión de Órdenes</p>
    </footer>
</body>
</html>
<?php
$conn->close();
?>


<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'embarque') {
    header("Location: login.html?unauthorized=1");
    exit();
}

include 'db_connect.php';

// Consulta para obtener las órdenes en estado 'enviado'
$result = $conn->query("SELECT * FROM ordenes WHERE estado = 'enviado'");
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Embarque</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h2>Listado de Órdenes para Envío</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Número de Orden</th>
                    <th>Prioridad</th>
                    <th>Fecha de Subida</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_number'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $row['prioridad'] ? 'Sí' : 'No' ?></td>
                        <td><?= htmlspecialchars($row['fecha_subida'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form action="server.php" method="post">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" name="mark_as_sent">Marcar como Enviado</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No hay órdenes pendientes para envío.</p>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>

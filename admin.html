<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrador') {
    header("Location: login.html?unauthorized=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Área de Administración</h1>
        <nav>
            <ul>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Gestión de Usuarios</h2>
        <form action="server.php" method="post">
            <label for="new_user">Nuevo Usuario:</label>
            <input type="text" id="new_user" name="new_user" required>
            <label for="new_password">Contraseña:</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="role">Área:</label>
            <select id="role" name="role" required>
                <option value="planeacion">Planeación</option>
                <option value="diseno">Diseño</option>
                <option value="maquetado">Maquetado</option>
                <option value="retrabajo">Retrabajo</option>
                <option value="impresion">Impresión</option>
                <option value="almacen">Almacén</option>
                <option value="embarques">Embarques</option>
            </select>
            <button type="submit" name="register">Registrar</button>
        </form>
        <h2>Buscar Orden</h2>
        <form action="server.php" method="get">
            <label for="order_number">Número de Orden:</label>
            <input type="text" id="order_number" name="order_number" required>
            <button type="submit" name="search_order">Buscar</button>
        </form>
        <script>
            const params = new URLSearchParams(window.location.search);
            if (params.get('success')) {
                alert('Acción realizada con éxito.');
            } else if (params.get('error')) {
                alert('Hubo un problema al realizar la acción.');
            }
        </script>
    </main>
    <footer>
        <p>&copy; 2025 Proyecto</p>
    </footer>
</body>
</html>

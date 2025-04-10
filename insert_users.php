<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'gestion_ordenes');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Conjunto de usuarios para insertar
$usuarios = [
    ['username' => 'planeacion', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'planeacion'],
    ['username' => 'diseno', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'diseno'],
    ['username' => 'maquetado', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'maquetado'],
    ['username' => 'retrabajo', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'retrabajo'],
    ['username' => 'impresion', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'impresion'],
    ['username' => 'almacen', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'almacen'],
    ['username' => 'embarques', 'password' => password_hash('12345', PASSWORD_DEFAULT), 'role' => 'embarques'],
];

// Utiliza una transacción para mayor seguridad y rendimiento
$conn->begin_transaction();

try {
    foreach ($usuarios as $usuario) {
        $stmt = $conn->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario['username'], $usuario['password'], $usuario['role']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al insertar el usuario: " . $usuario['username']);
        }
    }
    // Confirma los cambios
    $conn->commit();
    echo "Usuarios insertados correctamente.";
} catch (Exception $e) {
    // Revierte los cambios en caso de error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
} finally {
    // Cierra el statement y la conexión
    $stmt->close();
    $conn->close();
}
?>

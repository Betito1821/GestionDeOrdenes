<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y sanitizar los datos ingresados por el usuario
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);

    // Credenciales pre-generadas con contraseñas encriptadas (hashes estáticos)
    $users = [
        "administrador" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "admin.html"
        ],
        "planeacion" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "planeacion.php"
        ],
        "diseno" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "diseno.php"
        ],
        "maquetado" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "maquetado.php"
        ],
        "retrabajo" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "retrabajo.php"
        ],
        "impresion" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "impresion.php"
        ],
        "almacen" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "almacen.html"
        ],
        "embarque" => [
            "password" => '$2y$10$7QdTkIQY5zoLymCwcqBdfuuG6x8MqJ.k5W9MIwT5m6HGSGqw5EJM6', // Reemplaza con un hash generado
            "redirect" => "embarque.php"
        ]
    ];

    // Verificar si el usuario y la contraseña son válidos
    if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
        // Configurar la sesión si la autenticación es exitosa
        $_SESSION['usuario'] = $username; // Guardar el nombre del usuario en la sesión
        $_SESSION['role'] = $username;   // Guardar el rol (puedes personalizar esta lógica según los requisitos)
        
        // Redirigir a la página correspondiente
        header("Location: " . $users[$username]['redirect']);
        exit();
    } else {
        // Credenciales incorrectas, redirigir con mensaje de error
        header("Location: login.html?error=1");
        exit();
    }
} else {
    // Si la solicitud no es del tipo POST, redirigir al formulario de inicio de sesión
    header("Location: login.html");
    exit();
}
?>

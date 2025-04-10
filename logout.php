<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión por completo
header("Location: login.html"); // Redirige al formulario de login
exit();
?>

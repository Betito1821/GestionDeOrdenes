<?php
session_start();
$_SESSION['username'] = "usuario_prueba";
$_SESSION['role'] = "diseno";
header("Location: test_session_check.php");
exit();
?>

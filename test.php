<?php
session_start();
$_SESSION['test'] = "Funcionando correctamente";
header("Location: read.php");
exit();
?>

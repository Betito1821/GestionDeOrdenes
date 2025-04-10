<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_number = $_POST['order_number'];
    $stmt = $conn->prepare("UPDATE ordenes SET estado = 'en-proceso' WHERE order_number = ?");
    $stmt->bind_param("s", $order_number);
    $stmt->execute();
    header("Location: diseno.php");
    exit();
}
?>

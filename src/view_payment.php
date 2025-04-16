<?php
require_once 'admin_check.php';
require_once 'databases.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$payment = $conn->query("SELECT * FROM payments WHERE id = $id")->fetch_assoc();

if (!$payment) {
    header("Location: payment_records.php");
    exit();
}
?>

<!-- Detailed view HTML similar to the table row but expanded -->
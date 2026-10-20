<?php

session_start();

include 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "DELETE FROM transactions WHERE id='$id'";

mysqli_query($conn, $sql);

header("Location: dashboard.php");

exit();

?>
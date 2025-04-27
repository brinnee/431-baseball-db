<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zoo Homepage</title>
</head>
<body>
    <h1>Welcome to the Zoo Management System</h1>

    <?php if ($role == 'owner') { ?>
        <a href="owner_page.php">Owner Functions</a><br>
    <?php } ?>

    <?php if ($role == 'manager') { ?>
        <a href="manager_page.php">Manager Functions</a><br>
    <?php } ?>

    <?php if ($role == 'employee') { ?>
        <a href="employee_page.php">Employee Functions</a><br>
    <?php } ?>

    <?php if ($role == 'customer') { ?>
        <a href="customer_page.php">Customer Functions</a><br>
    <?php } ?>

    <a href="logout.php">Logout</a>
</body>
</html>

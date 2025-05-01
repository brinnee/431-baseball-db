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
    <title>Homepage</title>
</head>
<body>
    <h1>Welcome to the Baseball Management System</h1>

    <?php if ($role == 'manager') { ?>
        <a href="manager_page.php"> Manager Functions</a><br>
    <?php } ?>

    <?php if ($role == 'coach') { ?>
        <a href="teams.php">View Teams</a><br>
    <?php } ?>

    <?php if ($role == 'player') { ?>
        <a href="player_page.php">Player Functions</a><br>
    <?php } ?>

    <?php if ($role == 'visitor') { ?>
        <a href="visitor_page.php">Visitor Functions</a><br>
    <?php } ?>

    <a href="logout.php">Logout</a>
</body>
</html>

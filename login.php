<?php
session_start();
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'your_user', 'your_password', 'zoo_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT role_name FROM accounts WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->bind_result($role);

    if ($stmt->fetch()) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header('Location: homepage.php');
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>

<form method="post">
    Username: <input type="text" name="username" required><br/>
    Password: <input type="password" name="password" required><br/>
    <button type="submit">Login</button>
</form>

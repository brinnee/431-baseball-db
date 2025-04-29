<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config.php'; 

$DB_HOST = "localhost"; 
$DB_USER = "root"; 
$DB_PASSWORD = ""; 
$DB_NAME = "zoo_db"; 

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Step 1: Get password and role_id from Users
        $stmt = $conn->prepare("SELECT password, role_id FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password_from_db, $role_id);

        if ($stmt->fetch() && password_verify($password, $hashed_password_from_db)) {
            // Password is correct, now fetch the role name
            $stmt->close();
            $stmtRole = $conn->prepare("SELECT role_name FROM Roles WHERE ID = ?");
            $stmtRole->bind_param("i", $role_id);
            $stmtRole->execute();
            $stmtRole->bind_result($role_name);
            $stmtRole->fetch();
            $stmtRole->close();

            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role_name;
            header('Location: homepage.php');
            exit();
        } else {
            echo "Invalid username or password.";
        }
        $stmt->close();

    } elseif ($_POST['action'] == 'register') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        $employeeID = NULL; // assume NULL unless specified

        // Step 1: Check if username already exists
        $checkUserQuery = "SELECT ID FROM Users WHERE username = ?";
        $stmtCheck = $conn->prepare($checkUserQuery);
        $stmtCheck->bind_param("s", $username);
        $stmtCheck->execute();
        $stmtCheck->store_result();
    
        if ($stmtCheck->num_rows > 0) {
            echo "<script>alert('Error: Username already exists! Please log in.'); window.location.href='homepage.php';</script>";
            exit();
        }
        $stmtCheck->close();

        // Step 2: Get role ID
        $getRoleQuery = "SELECT ID FROM Roles WHERE role_name = ?";
        $stmtRole = $conn->prepare($getRoleQuery);
        $stmtRole->bind_param("s", $role);
        $stmtRole->execute();
        $stmtRole->bind_result($role_id);
        $stmtRole->fetch();
        $stmtRole->close();

        if (!$role_id) {
            echo "<script>alert('Error: Selected role is invalid.'); window.location.href='homepage.php';</script>";
            exit();
        }

        // Step 3: Insert new user
        $insertQuery = "INSERT INTO Users (username, password, role_id, employee_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ssii", $username, $hashed_password, $role_id, $employeeID);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='homepage.php';</script>";
        } else {
            die("Error: " . $stmt->error);
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login/Register</title>
</head>
<body>

<h2>Login</h2>
<form method="post">
    <label>Username:</label>
    <input type="text" name="username" required><br/>

    <label>Password:</label>
    <input type="password" name="password" required><br/>

    <button type="submit" name="action" value="login">Login</button>
</form>

<h2>Register</h2>
<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required><br/>

    <label>Password:</label>
    <input type="password" name="password" required><br/>

    <label>Role:</label>
    <select name="role" required>
        <option value="Employee">Employee</option>
        <option value="Visitor">Visitor</option>
        <option value="Manager">Manager</option>
    </select><br/><br/>

    <button type="submit" name="action" value="register">Register</button>
</form>

</body>
</html>

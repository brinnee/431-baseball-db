<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config.php'; 

$DB_HOST = "localhost"; 
$DB_USER = "root"; 
$DB_PASSWORD = ""; 
$DB_NAME = "baseball_db"; 

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
            header('Location: teams.php');
            exit();
        } else {
            echo "Invalid username or password.";
        }
        $stmt->close();

    } elseif ($_POST['action'] == 'register') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        

        //check if username already exists
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
        
        //getting role id
        $getRoleQuery = "SELECT ID FROM Roles WHERE LOWER(role_name) = LOWER(?)";
        $stmtRole = $conn->prepare($getRoleQuery);
        $stmtRole->bind_param("s", $role);
        $stmtRole->execute();
        $stmtRole->bind_result($role_id);

        if (!$stmtRole->fetch()) {
            echo "<pre>DEBUG: Role lookup failed. Value sent: '$role'</pre>";
            echo "<script>alert('Error: Selected role is invalid.'); window.location.href='homepage.php';</script>";
            $stmtRole->close();
            exit();
        }

        $stmtRole->close();

        //inserting new user
        $insertQuery = "INSERT INTO Users (username, password, role_id, member_id) VALUES ( ?, ?, ?, 1)";
        $stmt = $conn->prepare($insertQuery);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ssi", $username, $hashed_password, $role_id);

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
    <option value="manager">Manager</option>
    <option value="coach">Coach</option>
    <option value="player">Player</option>
    <option value="visitor">Visitor</option>
</select><br/><br/>

    <button type="submit" name="action" value="register">Register</button>
</form>

</body>
</html>

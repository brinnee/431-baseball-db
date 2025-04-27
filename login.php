<?php
session_start();
require_once 'config.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];

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
        $stmt->close();

    } elseif ($_POST['action'] == 'register') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        $employeeID = NULL; // assume NULL unless specified

        // check if username exists
        $checkUserQuery = "SELECT ID FROM Users WHERE username = ?";
        $stmtCheck = $conn->prepare($checkUserQuery);
        $stmtCheck->bind_param("s", $username);
        $stmtCheck->execute();
        $stmtCheck->store_result();
    
        if ($stmtCheck->num_rows > 0) {
            echo "<script>alert('Error: Username already exists! Please log in.'); window.location.href='signup.php';</script>";
            exit();
        }
        $stmtCheck->close();

        // insert user
        $insertQuery = "INSERT INTO Users (username, password, role_id, employee_id) VALUES (?, ?, (SELECT ID FROM Roles WHERE role_name = ?), ?)";
        $stmt = $conn->prepare($insertQuery);

        $hashed_password = $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssi", $username, $hashed_password, $role, $employeeID);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='signup.php';</script>";
        } else {
            die("Error: " . $stmt->error);
        }
        $stmt->close();
    }
}
?>
<h2>Login</h2>
<form method="post">
    Username: <input type="text" name="username" required><br/>
    Password: <input type="password" name="password" required><br/>
    <button type="submit" name="action" value="login">Login</button>
</form>

<h2>Register</h2>
    <form action="" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="player">Employee</option>
            <option value="coach">Visitor</option>
            <option value="manager">Manager</option>
        </select>
        <button type="submit" name="action" value="register">Register</button>
        <br><br>

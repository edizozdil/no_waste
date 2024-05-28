<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT PASSWORD FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->bind_result($hashed_password);
    if (!$stmt->fetch()) {
        echo "Invalid email or password.<br>";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['email'] = $email;
        header("Location: shop.php");
        exit();
    } else {
        echo "Invalid email or password.<br>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - No Waste</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header" style="justify-content: center;">
            <div class="logo"><img src="img/logo.png" alt="Logo" height="35px"> NO WASTE</div>
        </div>
        <div style="margin:80px;" class="content">
            <h1><i>LOGIN</i></h1>
            <form action="login.php" method="post">
                <div><input type="email" name="email" placeholder="Email" class="input-class" required></div>
                <div><input type="password" name="password" placeholder="Password" class="input-class" required></div>
                <button type="submit" class="login-button">Login</button>
            </form>
            <p><i>Don't have an account? <a class="small-buttons" href="signup.php">Create an account.</a></i></p>
        </div>
    </div>
</body>
</html>
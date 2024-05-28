<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $role = $_POST['role'];

    if ($password !== $repeat_password) {
        echo "Passwords do not match!";
        exit;
    }
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (email, NAME, surname, PASSWORD, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $name, $surname, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Sign-Up successful!";
    
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - No Waste</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header" style="justify-content: center;">
            <div class="logo"><img src="img/logo.png" alt="Logo" height="35px"> NO WASTE</div>
        </div>
        <div style="margin:40px;" class="content">
            <h1><i>SIGN UP</i></h1>
            <form action="signup.php" method="post">
                <div style="margin-left:60px;">
                    <label for="salesman"><input type="radio" name="role" value="salesman" id="salesman" checked> Salesman</label>
                    <label for="receiver"><input type="radio" name="role" value="receiver" id="receiver"> Receiver</label>
                </div>
                <div><input type="email" name="email" placeholder="Email" class="input-class" required></div>
                <div><input type="text" name="name" placeholder="Name" class="input-class" required></div>
                <div><input type="text" name="surname" placeholder="Surname" class="input-class" required></div>
                <div><input type="password" name="password" placeholder="Password" class="input-class" required></div>
                <div><input type="password" name="repeat_password" placeholder="Repeat Password" class="input-class" required></div>
                <button type="submit" class="login-button">Sign-Up</button>
            </form>
        </div>
    </div>
</body>
</html>
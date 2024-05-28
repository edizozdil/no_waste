<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $name_parts = explode(' ', $fullname);
    $name = $name_parts[0] ?? $user['NAME'];
    $surname = isset($name_parts[1]) ? $name_parts[1] : $user['surname'];
    $bio = $_POST['bio'] ?? $user['bio'];
    $location = $_POST['location'] ?? $user['location'];
    $new_email = $_POST['email'] ?? $user['email'];
    $current_password = $_POST['password'] ?? '';
    $new_password = $_POST['repeat_password'] ?? '';
    $profile_image = $user['profile_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "img/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        $profile_image = $_FILES["profile_image"]["name"];
    }

    if (!empty($current_password) && !empty($new_password)) {
        if (password_verify($current_password, $user['PASSWORD'])) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $conn->prepare("UPDATE users SET NAME = ?, surname = ?, bio = ?, location = ?, email = ?, PASSWORD = ?, profile_image = ? WHERE id = ?");
            $update_stmt->bind_param("sssssssi", $name, $surname, $bio, $location, $new_email, $hashed_password, $profile_image, $user['id']);
        } else {
            $error = "Current password is incorrect.";
        }
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET NAME = ?, surname = ?, bio = ?, location = ?, email = ?, profile_image = ? WHERE id = ?");
        $update_stmt->bind_param("ssssssi", $name, $surname, $bio, $location, $new_email, $profile_image, $user['id']);
    }

    if (isset($update_stmt) && $update_stmt->execute()) {
        $_SESSION['email'] = $new_email;
        header("Location: profile.php");
        exit();
    } else {
        $error = $error ?? "Failed to update profile.";
    }
    if (isset($update_stmt)) $update_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - No Waste</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .password-container {
            position: relative;
        }
    </style>
    <script>
        function togglePassword(id) {
            var input = document.getElementById(id);
            var icon = document.getElementById(id + '-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = '🙈';
            } else {
                input.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="img/logo.png" alt="Logo" height="35px"> NO WASTE</div>
            <div class="nav">
                <a href="shop.php">SHOP</a>
                <a href="profile.php">PROFILE</a>
                <a href="contact.php">CONTACT</a>
                <a href="menu.php"><img src="img/download.svg" alt="Upload" height="25px"> </a>
                <a href="settings.php"> <img src="img/settings.png" alt="Settings" height="25px"> </a>
            </div>
        </div>
        <div>
            <div style="float:left;width:30%;">
                <img src="img/<?php echo htmlspecialchars($user['profile_image'] ?? 'farmer.jpg'); ?>" alt="Profile Picture" class="profile-pic">
                <h2>Welcome! <?php echo htmlspecialchars($user['NAME'] . ' ' . $user['surname']); ?></h2>
                <p>You have <?php echo htmlspecialchars($user['points']); ?> points!</p>
            </div>
        </div>
        <div>
            <div class="settings-form">
                <form action="settings.php" method="post" enctype="multipart/form-data">
                    <div style="float:left;width:35%;margin-top:50px;">
                        <h2 style="padding:10px;">Account Settings</h2>
                        <div><input type="email" class="input-class" name="email" placeholder="E-Mail" value="<?php echo htmlspecialchars($user['email']); ?>"></div>
                        <div class="password-container">
                            <input type="password" class="input-class" id="current_password" name="password" placeholder="Current Password">
                            <span id="current_password-icon" onclick="togglePassword('current_password')">👁️</span>
                        </div>
                        <div class="password-container">
                            <input type="password" class="input-class" id="new_password" name="repeat_password" placeholder="New Password">
                            <span id="new_password-icon" onclick="togglePassword('new_password')">👁️</span>
                        </div>
                    </div>
                    <div>
                        <div style="float:left;width:30%;margin-top:50px;">
                            <h2 style="padding:10px;">Profile Settings</h2>
                            <div><input type="text" class="input-class" name="fullname" placeholder="Full Name" value="<?php echo htmlspecialchars($user['NAME'] . ' ' . $user['surname']); ?>"></div>
                            <div><input type="text" class="input-class" name="bio" placeholder="Bio" value="<?php echo htmlspecialchars($user['bio'] ?? ''); ?>"></div>
                            <div><input type="text" class="input-class" name="location" placeholder="Location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"></div>
                            <div style="padding:10px;"><input type="file" name="profile_image"></div>
                        </div>
                    </div>
                    <div style="text-align: end;padding-top:350px;"><button type="submit" class="button">Update Profile</button></div>
                </form>
                <form action="logout.php" method="post">
                    <div style="text-align: end; padding-top:80px"><button type="submit" class="button">Sign Out</button></div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

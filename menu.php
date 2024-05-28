<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$hide_home_button = in_array($current_page, ['shop.php', 'profile.php', 'contact.php', 'menu.php', 'upload.php', 'upload_orange.php', 'upload_potato.php', 'upload_apples.php' ,'settings.php']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Menu - No Waste</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="img/logo.png" alt="Logo" height="35px"> NO WASTE</div>
            <div class="nav">
                <?php if (!$hide_home_button): ?>
                    <a href="index.php">HOME</a>
                <?php endif; ?>
                <a href="shop.php">SHOP</a>
                <a href="profile.php">PROFILE</a>
                <a href="contact.php">CONTACT</a>
                <a href="menu.php"><img src="img/download.svg" alt="Upload" height="25px"> </a>
                <a href="settings.php"> <img src="img/settings.png" alt="Settings" height="25px"> </a>          </div>
        </div>
        <div class="content">
            <div class="fruit-container">
                <div class="fruit">
                    <img src="img/orange.jpg" alt="Orange">
                    <button onclick="location.href='upload_orange.php'">ORANGE</button>
                </div>
                <div class="fruit">
                    <img src="img/banana.jpg" alt="Banana">
                    <button onclick="location.href='upload.php'">BANANA</button>
                </div>
                <div class="fruit">
                    <img src="img/potato.jpg" alt="Potato">
                    <button onclick="location.href='upload_potato.php'">POTATO</button>
                </div>
            </div>
            <div class="fruit-container">
                <div class="fruit">
                    <img src="img/olives.jpg" alt="Olives">
                    <button onclick="location.href='upload_olives.php'">OLIVES</button>
                </div>
                <div class="fruit">
                    <img src="img/grapes.jpg" alt="Grapes">
                    <button onclick="location.href='upload_grapes.php'">GRAPES</button>
                </div>
                <div class="fruit">
                    <img src="img/apple.jpg" alt="Apple">
                    <button onclick="location.href='upload_apples.php'">APPLE</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
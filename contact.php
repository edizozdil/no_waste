<?php
include 'db.php';

$showModal = false; 
$modalMessage = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $modalMessage = "Thank you for reaching us. We will get back to you soon!";
        $showModal = true;
    } else {
        $modalMessage = "There was an error submitting your message. Please try again.";
        $showModal = true;
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
    <title>Contact - No Waste</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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
                <a href="settings.php"> <img src="img/settings.png" alt="Settings" height="25px"> </a>   </div>
        </div>
        <div>
            <div style="float:left;width:40%;margin-top:30px;">
                <h1>Contact</h1>
                <p>Please fill out the form below to send us an email.</p>
                <p>Thank you for reaching out to us. Your input helps us improve and serves as a guide for delivering better experiences. We appreciate your time and interest in our service.</p>
                <p>If you prefer direct communication, feel free to reach us via email. We're looking forward to hearing from you soon!</p>
                <p><strong>E-mail:</strong><br>contact@nowaste.com</p>
            </div>
            <div style="float:right;width:50%;">
                <form action="contact.php" method="post">
                <div class="form-container">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <div style="float:right;"><button class="submit-button" type="submit">SUBMIT</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p><?php echo htmlspecialchars($modalMessage); ?></p>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }

        <?php if ($showModal): ?>
            document.getElementById('myModal').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>

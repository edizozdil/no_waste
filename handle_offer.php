<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $offer_id = $_POST['offer_id'];

    if ($action === 'accept') {
        // Fetch user details
        $stmt = $conn->prepare("SELECT users.name, users.surname, users.email, users.location FROM offers JOIN users ON offers.user_email = users.email WHERE offers.id = ?");
        $stmt->bind_param("i", $offer_id);
        $stmt->execute();
        $stmt->bind_result($name, $surname, $email, $location);
        $stmt->fetch();
        $stmt->close();

        // Set the notification message
        $_SESSION['notification'] = "Name: $name<br>Surname: $surname<br>E-Mail: $email<br>Location: $location";

        // Update the status to accepted
        $stmt = $conn->prepare("UPDATE offers SET status = 'accepted' WHERE id = ?");
    } else {
        // Update the status to rejected
        $stmt = $conn->prepare("UPDATE offers SET status = 'rejected' WHERE id = ?");
    }
    $stmt->bind_param("i", $offer_id);

    if ($stmt->execute()) {
        header("Location: shop.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

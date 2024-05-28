<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $offer_price_option = $_POST['offer_price'];
    $user_email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($original_price);
    $stmt->fetch();
    $stmt->close();

    switch ($offer_price_option) {
        case '5':
            $final_price = $original_price * 0.95;
            break;
        case '10':
            $final_price = $original_price * 0.90;
            break;
        case '15':
            $final_price = $original_price * 0.85;
            break;
        default:
            $final_price = $original_price;
    }

    $stmt = $conn->prepare("INSERT INTO offers (product_id, original_price, offered_price, user_email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idds", $product_id, $original_price, $final_price, $user_email);

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

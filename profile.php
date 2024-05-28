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

$user_id = $user['id'];

$needs_stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ? AND type = 'Need'");
$needs_stmt->bind_param("i", $user_id);
$needs_stmt->execute();
$needs_result = $needs_stmt->get_result();

$needs = [];
while ($need = $needs_result->fetch_assoc()) {
    $needs[] = $need;
}
$needs_stmt->close();

$sales_stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ? AND type = 'Sell'");
$sales_stmt->bind_param("i", $user_id);
$sales_stmt->execute();
$sales_result = $sales_stmt->get_result();

$sales = [];
while ($sale = $sales_result->fetch_assoc()) {
    $sales[] = $sale;
}
$sales_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - No Waste</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="img/logo.png" alt="Logo" height="35px"> NO WASTE</div>
            <div class="nav">
                <a href="shop.php">SHOP</a>
                <a href="profile.php">PROFILE</a>
                <a href="contact.php">CONTACT</a>
                <a href="menu.php"><img src="img/download.svg" alt="Upload" height="25px"></a>
                <a href="settings.php"><img src="img/settings.png" alt="Settings" height="25px"></a>
            </div>
        </div>
        <div>
            <div style="float:left;width: 30%;">
                <img src="img/<?php echo htmlspecialchars($user['profile_image'] ?? 'default.png'); ?>" alt="Profile Picture" class="profile-pic">
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars(($user['NAME'] ?? '') . " " . ($user['surname'] ?? '')); ?></h1>
                    <p><?php echo htmlspecialchars($user['bio'] ?? ''); ?></p>
                    <p><strong><?php echo htmlspecialchars($user['location'] ?? ''); ?></strong></p>
                    <p><strong>Mail: <?php echo htmlspecialchars($user['email'] ?? ''); ?></strong></p>
                    <p><strong>Sales Rate: </strong><span style='font-size:200%;color:khaki;'>&starf;&starf;&starf;&starf;&starf;</span></p>
                </div>
            </div>
            <div style="float:left;width: 35%;">
                <h2>Needs</h2>
                <div class="profile-products">
                    <?php foreach ($needs as $need): ?>
                        <div>
                            <img class="product-image" src="img/<?php echo htmlspecialchars($need['image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($need['name'] ?? ''); ?>">
                            <p><?php echo htmlspecialchars($need['name'] ?? ''); ?> - <?php echo htmlspecialchars($need['kilogram'] ?? 0); ?> kg</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="float:left;width:35%;">
                <h2>Sales</h2>
                <div class="profile-products">
                    <?php foreach ($sales as $sale): ?>
                        <div>
                            <img class="product-image" src="img/<?php echo htmlspecialchars($sale['image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($sale['name'] ?? ''); ?>">
                            <p><?php echo htmlspecialchars($sale['name'] ?? ''); ?> - <?php echo htmlspecialchars($sale['kilogram'] ?? 0); ?> kg</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

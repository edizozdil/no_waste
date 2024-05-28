<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY created_at DESC");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$offers_stmt = $conn->prepare("SELECT offers.*, products.name FROM offers JOIN products ON offers.product_id = products.id WHERE offers.status = 'pending'");
$offers_stmt->execute();
$offers_result = $offers_stmt->get_result();
$offers = $offers_result->fetch_all(MYSQLI_ASSOC);
$offers_stmt->close();

$conn->close();

$notification = isset($_SESSION['notification']) ? $_SESSION['notification'] : '';

$current_page = basename($_SERVER['PHP_SELF']);
$hide_home_button = in_array($current_page, ['shop.php', 'profile.php', 'contact.php', 'menu.php', 'upload.php', 'upload_orange.php', 'upload_potato.php', 'upload_grapes.php', 'upload_olives.php', 'settings.php']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - No Waste</title>
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

        .notifications {
            position: relative;
            display: inline-block;
        }

        .notification-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 250px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .notifications:hover .notification-content {
            display: block;
        }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-actions {
            margin-top: 10px;
        }
    </style>
    <script>
        function openModal(productId, price) {
            document.getElementById('productId').value = productId;
            document.getElementById('fullPrice').innerText = price.toFixed(2) + ' ₺';
            document.getElementById('discount5').innerText = (price * 0.95).toFixed(2) + ' ₺';
            document.getElementById('discount10').innerText = (price * 0.90).toFixed(2) + ' ₺';
            document.getElementById('discount15').innerText = (price * 0.85).toFixed(2) + ' ₺';
            document.getElementById('offerModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('offerModal').style.display = 'none';
        }

        function sendOffer() {
            var form = document.getElementById('offerForm');
            form.submit();
        }

        function handleOffer(action, offerId) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'handle_offer.php';
            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            var offerIdInput = document.createElement('input');
            offerIdInput.type = 'hidden';
            offerIdInput.name = 'offer_id';
            offerIdInput.value = offerId;
            form.appendChild(offerIdInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
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
                <a href="settings.php"> <img src="img/settings.png" alt="Settings" height="25px"> </a>
            </div>
        </div>
        <div class="content">
          <div style="float:left;width:100%;">
                <form method="get" action="shop.php">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="notifications">
                <span> <h1>ONLINE SHOP 🔔 </h1> </span>
                <div class="notification-content">
                    <?php if ($notification): ?>
                        <div class="notification-item">
                            <?php echo $notification; ?>
                            <?php unset($_SESSION['notification']); ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($offers as $offer): ?>
                            <div class="notification-item">
                                <p>You have %<?php echo (100 - ($offer['offered_price'] / $offer['original_price'] * 100)); ?> discounted offer for your product!</p>
                                <div class="notification-actions">
                                    <button onclick="handleOffer('accept', <?php echo $offer['id']; ?>)">Approve</button>
                                    <button onclick="handleOffer('reject', <?php echo $offer['id']; ?>)">Reject</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="products">
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img class="product-image" src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                        <p><?php echo htmlspecialchars($product['kilogram']); ?> kg - <?php echo htmlspecialchars($product['conversion_method']); ?></p>
                        <p><?php echo htmlspecialchars($product['price']); ?> ₺</p>
                        <p><?php echo htmlspecialchars($product['type']); ?></p> <!-- Display Need or Sell -->
                        <button class="button" onclick="openModal(<?php echo $product['id']; ?>, <?php echo $product['price']; ?>)">Give Offer</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="offerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Send Offer</h2>
            <form id="offerForm" action="send_offer.php" method="post">
                <input type="hidden" id="productId" name="product_id">
                <p><input type="radio" name="offer_price" value="full" required> Full Price: <span id="fullPrice"></span></p>
                <p><input type="radio" name="offer_price" value="5" required> %5 discount <span id="discount5"></span></p>
                <p><input type="radio" name="offer_price" value="10" required> %10 discount <span id="discount10"></span></p>
                <p><input type="radio" name="offer_price" value="15" required> %15 discount <span id="discount15"></span></p>
                <button type="button" onclick="sendOffer()">Send</button>
            </form>
        </div>
    </div>
</body>
</html>

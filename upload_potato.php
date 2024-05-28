<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    include 'db.php';

    $kilogram = $_POST['kilogram'];
    $conversion_method = $_POST['conversion_method'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $image = 'potato.jpg';

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, type, conversion_method, kilogram) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $name = "Potato";
    $description = "Potato recycling, particularly of peels and leftover scraps, is a valuable practice for sustainability. Potato by-products can be utilized to produce biodegradable packaging, biofuels, and even cosmetics. Additionally, potato waste is often processed into animal feed and organic fertilizers. Recycling potatoes not only minimizes food waste but also fosters the development of eco-friendly materials, supporting a circular economy and reducing environmental impact.";
    $stmt->bind_param("ssisssi", $name, $description, $price, $image, $type, $conversion_method, $kilogram);

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Patato Product - No Waste</title>
    <link rel="stylesheet" href="style.css">
    <style>
	    input::-webkit-inner-spin-button {
		    display: none;
	}
	</style>
    <script>
        function updateScoresAndPrice() {
            const conversionMethod = document.getElementById('conversionMethods').value;
            const kilogram = document.getElementById('kilogram').value;
            let economicScore, socialScore, environmentalScore, pricePerKg;

            switch (conversionMethod) {
                case 'Cleaning Agents':
                    economicScore = 2;
                    socialScore = 4;
                    environmentalScore = 4;
                    pricePerKg = 100;
                    break;
                case 'Construction Materials':
                    economicScore = 4;
                    socialScore = 3;
                    environmentalScore = 5;
                    pricePerKg = 400;
                    break;
                case 'Cosmetics/Pharmaceutical':
                    economicScore = 4;
                    socialScore = 3;
                    environmentalScore = 2;
                    pricePerKg = 500;
                    break;
                case 'Natural Dye':
                    economicScore = 2;
                    socialScore = 4;
                    environmentalScore = 4;
                    pricePerKg = 150;
                    break;
                default:
                    economicScore = '-';
                    socialScore = '-';
                    environmentalScore = '-';
                    pricePerKg = 0;
            }

            document.getElementById('economicScore').innerHTML = calculateStar(economicScore);
            document.getElementById('socialScore').innerHTML = calculateStar(socialScore);
            document.getElementById('environmentalScore').innerHTML = calculateStar(environmentalScore);
            document.getElementById('price').innerText = `${kilogram * pricePerKg} ₺`; 
        }

        function calculateStar(score) {
	        let text = "";
		    for (let i = 0; i < score; i++) {
		         text += "<span style='font-size:150%;color:khaki;'>&starf;</span>";
		    }  
	        return text;
        };
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
            <div style="float:left;width:24%;">
                <img class="product-image" src="img/potato.jpg" alt="Potato">
                <p>&nbsp;Potato recycling, particularly of peels and leftover scraps, is a valuable practice for sustainability. Potato by-products can be utilized to produce biodegradable packaging, biofuels, and even cosmetics. Additionally, potato waste is often processed into animal feed and organic fertilizers. Recycling potatoes not only minimizes food waste but also fosters the development of eco-friendly materials, supporting a circular economy and reducing environmental impact.</p>
            </div>
            <div>
                <div style="float:right;width:70%;">
                <form action="upload_potato.php" method="post" enctype="multipart/form-data">
                    <div style="margin-top:20px;">
                        <div class="separator">
                            <label><input type="radio" name="type" value="Need" required> Need</label>
                            <label><input type="radio" name="type" value="Sell" required> Sell</label>
                        </div>
                    <p><i>Enter your products kilogram:</i></p>
                    <input type="number" id="kilogram" name="kilogram" oninput="updateScoresAndPrice()" required>
                    </div>
                    <div class="separator">
                    <h4>Conversion Methods</h4>
                    <select id="conversionMethods" name="conversion_method" onchange="updateScoresAndPrice()" required>
                        <option value="" disabled selected>------Select a method------</option>
                        <option value="Cleaning Agents">Cleaning Agents</option>
                        <option value="Construction Materials">Construction Materials</option>
                        <option value="Cosmetics/Pharmaceutical">Cosmetics/Pharmaceutical</option>
                        <option value="Natural Dye">Natural Dye</option>
                    </select>
                    </div>
                    <div class="separator">
                    <h4>Price</h4>
                    <p id="price">_ ₺</p>
                    <input type="hidden" id="price_hidden" name="price">
                    </div>
                    <div style="margin-top:70px;">
                        <div class="separator">
                            <h4>Scores</h4>
                            <div style="float:left;width: 150px;">
                                <p>Economic Score: <span id="economicScore">-</span></p>
                            </div>
                            <div style="float:left;width: 180px;">
                                <p>Environmental Score: <span id="environmentalScore">-</span></p>
                            </div>
                            <div style="float:left;width: 110px;">
                                <p>Social Score: <span id="socialScore">-</span></p>
                            </div>
                        </div>
                    <div style="text-align: end;">
                        <div style="margin-top:45px;">
                            <button class="button" type="submit" name="submit">SAVE</button>
                            <button class="button" type="button" onclick="location.href='shop.php'">BACK</button>
                        </div>
                    </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('form').onsubmit = function() {
            document.getElementById('price_hidden').value = document.getElementById('price').innerText;
        };
    </script>
</body>
</html>


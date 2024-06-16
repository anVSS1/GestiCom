<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: welcome.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "g_client";

$errorMsg = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the product ID is provided in the URL parameter
    if (isset($_GET['id'])) {
        $productId = $_GET['id'];

        // Fetch the product details from the database
        $sql = "SELECT id_produit, nom_produit, prix_uni, quantite, description, image_url
                FROM articles
                WHERE id_produit = :id";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':id', $productId, PDO::PARAM_INT);
        $statement->execute();
        $product = $statement->fetch(PDO::FETCH_OBJ);

        // Check if the product exists
        if (!$product) {
            $errorMsg = "Product not found.";
        }
    } else {
        $errorMsg = "Product ID not provided.";
    }
} catch (PDOException $e) {
    $errorMsg = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $errorMsg = "An error occurred: " . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 960px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .product-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product-image {
            max-width: 300px;
            max-height: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-info {
            flex: 1;
        }

        .product-info h2 {
            margin-top: 0;
        }

        .product-info p {
            margin: 5px 0;
        }

        .product-description {
            border-top: 1px solid #ddd;
            margin-top: 20px;
            padding-top: 20px;
        }

        .error-message {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($errorMsg)) : ?>
            <div class="error-message">
                <?php echo $errorMsg; ?>
            </div>
        <?php else : ?>
            <div class="product-details">
                <div class="product-image">
                    <!-- Display product image -->
                    <?php
                    $productImagePath = 'images/' . $product->image_url;
                    // Check if the image file exists
                    if (file_exists($productImagePath)) {
                        echo '<img src="' . $productImagePath . '" alt="' . $product->nom_produit . '" class="product-image">';
                    } else {
                        echo '<p>Image not found</p>';
                    }
                    ?>
                </div>
                <div class="product-info">
                    <h2><?php echo $product->nom_produit; ?></h2>
                    <p><strong>Price:</strong> <?php echo $product->prix_uni; ?> DH</p>
                    <p><strong>Stock:</strong> <?php echo $product->quantite; ?></p>
                    <div class="product-description">
                        <p><strong>Description:</strong></p>
                        <p><?php echo $product->description; ?></p>
                        <!-- Add any other product details you want to display -->
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>
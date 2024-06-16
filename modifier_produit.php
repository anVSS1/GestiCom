<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    $loggedInUser = $_SESSION['email'];
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: welcome.php");
    exit();
}

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Retrieve the product details from the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "g_client";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id_produit, nom_produit, prix_uni
                FROM articles
                WHERE id_produit = :id";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':id', $productId);
        $statement->execute();
        $product = $statement->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
    $conn = null;

    // Check if the product exists
    if (!$product) {
        echo "Product not found.";
        exit();
    }
} else {
    echo "Product ID not provided.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the modified product details from the form
    $modifiedName = $_POST['name'];
    $modifiedPrice = $_POST['price'];

    // Update the product in the database
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE articles
                SET nom_produit = :name, prix_uni = :price
                WHERE id_produit = :id";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':name', $modifiedName);
        $statement->bindValue(':price', $modifiedPrice);
        $statement->bindValue(':id', $productId);
        $statement->execute();

        // Redirect to the products page after successful modification
        header("Location: all_produits.php");
        exit();
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Modifier le produit</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nom du produit:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $product->nom_produit ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Prix unitaire:</label>
            <input type="text" class="form-control" id="price" name="price" value="<?= $product->prix_uni ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="all_produits.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
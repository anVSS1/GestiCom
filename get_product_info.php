<?php
// Assuming you have a database connection established already
// Include the necessary files and configuration as needed

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Perform a database query to retrieve product information based on the product ID
    // Replace the following code with your actual database query
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "g_client";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id_produit, nom_produit, prix_uni, quantite, description
                FROM articles
                WHERE id_produit = :productId"; // Assuming the column name for product ID is 'id_produit'

        $statement = $conn->prepare($sql);
        $statement->bindValue(':productId', $productId);
        $statement->execute();

        $product = $statement->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Product information found, return it as a JSON response
            header('Content-Type: application/json');
            echo json_encode($product);
        } else {
            // Product not found, return an error message
            $error = array('error' => 'Product not found');
            header('Content-Type: application/json');
            echo json_encode($error);
        }
    } catch (PDOException $e) {
        // Database error, return an error message
        $error = array('error' => 'Database error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($error);
    }
} else {
    // Product ID parameter not provided, return an error message
    $error = array('error' => 'Product ID parameter missing');
    header('Content-Type: application/json');
    echo json_encode($error);
}

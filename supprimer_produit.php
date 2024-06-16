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

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "g_client";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the id parameter is provided in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "DELETE FROM articles WHERE id_produit = :id";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $rowCount = $statement->rowCount();

        if ($rowCount > 0) {
            // Success message
            $successMsg = "Produit supprimé avec succès.";
        } else {
            // Error message
            $errorMsg = "Echec de la suppression du produit. Veuillez réessayer.";
        }
    } else {
        // Error message
        $errorMsg = "Product ID is missing.";
    }
} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
}
$conn = null;

// Redirect back to the previous page
header("Location: all_produits.php?errorMsg=" . urlencode($errorMsg) . "&successMsg=" . urlencode($successMsg));
exit();
?>

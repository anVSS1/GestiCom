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

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $commandeId = $_GET['id'];

        // Delete the command with the specified ID
        $sql = "DELETE FROM commande WHERE id_commande = :commandeId";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':commandeId', $commandeId);
        $statement->execute();

        // Redirect back to the index.php after deletion
        header("Location: all_commandes.php");
        exit();
    } else {
        // Redirect back to the index.php if command ID is not provided
        header("Location: all_commandes.php");
        exit();
    }
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>

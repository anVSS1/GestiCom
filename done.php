<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "g_client";

if (isset($_GET['id'])) {
    $commandId = $_GET['id'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update the status to 'done'
        $updateQuery = "UPDATE commande SET status = 'done' WHERE id_commande = :commandId";
        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bindValue(':commandId', $commandId);
        $updateStatement->execute();

        // Redirect back to the command list page
        header("Location: all_commandes.php");
        exit();
    } catch (PDOException $e) {
        echo $updateQuery . "<br>" . $e->getMessage();
    }
    $conn = null;
}

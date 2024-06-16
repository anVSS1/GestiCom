<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (isset($_SESSION['email'])) {
    $loggedInUser = $_SESSION['email'];
} else {
    // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
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
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_commande = $_POST["id_commande"];
    $montant_paye = $_POST["montant_paye"];
    $date_paiement = $_POST["date_paiement"];
    $mode_paiement = $_POST["mode_paiement"];
    $autres_informations = $_POST["autres_informations"];

    $sql = "INSERT INTO paiements (ID_commande, montant_paye, date_paiement, mode_paiement, autres_informations) VALUES (:id_commande, :montant_paye, :date_paiement, :mode_paiement, :autres_informations)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bindParam(":id_commande", $id_commande);
        $stmt->bindParam(":montant_paye", $montant_paye);
        $stmt->bindParam(":le_reste", $le_reste);
        $stmt->bindParam(":date_paiement", $date_paiement);
        $stmt->bindParam(":mode_paiement", $mode_paiement);
        $stmt->bindParam(":autres_informations", $autres_informations);

        if ($stmt->execute()) {
            // Rediriger vers une page appropriée après l'ajout réussi du paiement
            header("location: paiments.php");
            exit();
        } else {
            echo "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
        }
    }

    // Fermer la requête
    $stmt = null;
}

// Fermer la connexion
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Paiement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Ajouter un Paiement</h2>
    <form action="ajouter_paiement.php" method="post">
        <div class="form-group">
            <label for="id_commande">ID de la Commande:</label>
            <input type="text" class="form-control" id="id_commande" name="id_commande" required>
        </div>
        <div class="form-group">
            <label for="montant_paye">Montant Payé:</label>
            <input type="text" class="form-control" id="montant_paye" name="montant_paye" required>
        </div>
        <div class="form-group">
            <label for="le_reste">le reste:</label>
            <input type="text" class="form-control" id="le_reste" name="le_reste" required>
        </div>
        <div class="form-group">
            <label for="date_paiement">Date du Paiement:</label>
            <input type="date" class="form-control" id="date_paiement" name="date_paiement" required>
        </div>
        <div class="form-group">
            <label for="mode_paiement">Mode de Paiement:</label>
            <input type="text" class="form-control" id="mode_paiement" name="mode_paiement" required>
        </div>
        <div class="form-group">
            <label for="autres_informations">Autres Informations:</label>
            <textarea class="form-control" id="autres_informations" name="autres_informations" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter Paiement</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

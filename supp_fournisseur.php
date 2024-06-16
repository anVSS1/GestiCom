<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: welcome.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "g_client";

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM fournisseurs WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Le fournisseur a été supprimé avec succès.'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Une erreur s\'est produite lors de la suppression du fournisseur.'
        ];
    }

    header("Location: fournisseurs.php"); // Redirigez vers la liste des fournisseurs
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Fournisseur</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre fichier de styles CSS -->
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Supprimer un Fournisseur</h2>
        </div>
        <div class="card-body">
            <p>Voulez-vous vraiment supprimer ce fournisseur ?</p>
            <a href="supp_fournisseur.php?id=<?php echo $_GET['id']; ?>" class="btn btn-danger">Supprimer</a>
            <a href="fournisseurs.php" class="btn btn-secondary">Annuler</a>
        </div>
    </div>
</div>

</body>
</html>

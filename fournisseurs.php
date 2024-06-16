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

$sql = "SELECT * FROM fournisseurs";
$stmt = $conn->prepare($sql);
$stmt->execute();
$fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Fournisseurs</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre fichier de styles CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="fournisseur.php">Fournisseurs</a>
                </li>
                <!-- Ajoutez d'autres liens de navigation ici -->
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Liste des Fournisseurs</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fournisseurs as $fournisseur) : ?>
                <tr>
                    <td><?php echo $fournisseur['id']; ?></td>
                    <td><?php echo $fournisseur['nom']; ?></td>
                    <td><?php echo $fournisseur['adresse']; ?></td>
                    <td><?php echo $fournisseur['telephone']; ?></td>
                    <td><?php echo $fournisseur['email']; ?></td>
                    <td>
                        <a href="modifier_fournisseur.php?id=<?php echo $fournisseur['id']; ?>" class="btn btn-primary">Modifier</a>
                        <a href="supp_fournisseur.php?id=<?php echo $fournisseur['id']; ?>" class="btn btn-danger">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

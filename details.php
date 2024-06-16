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

        // Retrieve the details of the selected command
        $sql = "SELECT c.id_commande, c.date, c.observation, c.total_prix, cl.name AS client_name
                FROM commande c
                JOIN client cl ON c.client_id = cl.id
                WHERE c.id_commande = :commandeId";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':commandeId', $commandeId);
        $statement->execute();
        $command = $statement->fetch(PDO::FETCH_OBJ);
        
        // Retrieve the items in the command
        $sql = "SELECT a.nom_produit, d.quantite, d.total, d.prix_unitaire
        FROM details d
        JOIN articles a ON d.id_produit = a.id_produit
        WHERE d.id_commande = :commandeId";
        $itemStatement = $conn->prepare($sql);
        $itemStatement->bindValue(':commandeId', $commandeId);
        $itemStatement->execute();
        $items = $itemStatement->fetchAll(PDO::FETCH_OBJ);
    } else {
        // Redirect back to the index.php if command ID is not provided
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details de commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            background-color: #150926;
            color: white;
            padding: 15px;
        }
        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        .table-container {
            margin-top: 30px;
        }
        .table-container h5 {
            margin-bottom: 20px;
        }
        .btn-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <img src="logo.png" alt="Logo" class="logo">
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="index.php" class="nav-link px-2 text-white">Home</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container table-container">
        <h5>Details de commande</h5>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <th>Commande ID</th>
                    <td><?= $command->id_commande ?></td>
                </tr>
                <tr>
                    <th>Nom de client</th>
                    <td><?= $command->client_name ?></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><?= $command->date ?></td>
                </tr>
                <tr>
                    <th>Observation</th>
                    <td><?= $command->observation ?></td>
                </tr>
                <tr>
                    <th>Montant total</th>
                    <td><?= $command->total_prix ?> DH</td>
                </tr>
            </tbody>
        </table>

        <h5>Prpduits de commande</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantite</th>
                    <th>prix </th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item->nom_produit ?></td>
                            <td><?= $item->quantite ?></td>
                            <td><?= $item->prix_unitaire ?> DH</td>
                            <td><?= $item->total ?> DH</td> 
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Pas de Produits.</td> 
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center btn-container">
            <a href="all_commandes.php" class="btn btn-primary">Retourner vers les commandes</a>
            <a href="add_details.php?id=<?= $command->id_commande ?>" class="btn btn-primary">Ajouter des produits</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

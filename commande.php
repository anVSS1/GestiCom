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
        $clientId = $_GET['id'];

        // Retrieve the command details for the specified client
        $sql = "SELECT c.id_commande AS id_commande, c.date, c.observation, c.total_prix, c.status, cl.name AS client_name
                FROM commande c
                JOIN client cl ON c.client_id = cl.id
                WHERE cl.id = :clientId";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':clientId', $clientId);
        $statement->execute();
        $commands = $statement->fetchAll(PDO::FETCH_OBJ);
    } else {
        // Redirect back to the index.php if client ID is not provided
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
    <title>Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .bg-dark {
            background-color: #150926 !important;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <img src="logo.png" alt="Logo" width="40" height="40">
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="menu.php" class="nav-link px-2 text-white">Menu</a></li>
                    <li><a href="index.php" class="nav-link px-2 text-white">Clients</a></li>
                    <li><a href="all_produits.php" class="nav-link px-2 text-white">Produits</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container my-5">
        <?php if (empty($commands)) : ?>
            <div class="alert alert-info" role="alert">
                Pas de commandes.
            </div>
            <a href="ajouter_commande.php?client_id=<?php echo $clientId; ?>" class="btn btn-primary">Ajouter une Commande</a>        <?php else : ?>
            <h5>Commandes du client:</h5>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Commande ID</th>
                        <th>Client Name</th>
                        <th>Date</th>
                        <th>Observation</th>
                        <th>Prix total</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commands as $command) : ?>
                        <tr>
                            <td><?= $command->id_commande ?></td>
                            <td><?= $command->client_name ?></td>
                            <td><?= $command->date ?></td>
                            <td><?= $command->observation ?></td>
                            <td><?= $command->total_prix ?> DH</td>
                            <td>
                                <?php if ($command->status === 'en cours') : ?>
                                    <button class="btn btn-warning btn-sm" disabled>En Cours</button>
                                    <a href="done.php?id=<?= $command->id_commande ?>" class="btn btn-success btn-sm">Marquer comme terminé</a>
                                <?php else : ?>
                                    <button class="btn btn-success btn-sm" disabled>Done</button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="details.php?id=<?= $command->id_commande ?>" class="btn btn-secondary btn-sm" title="Détails">
                                    <i class="bi bi-info-circle-fill"></i>
                                </a>
                                <a href="supprimer_commande.php?id=<?= $command->id_commande ?>" class="btn btn-danger btn-sm" title="Delete Commande">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                                <a href="generate_pdf.php?id=<?= $command->id_commande ?>" target="_blank" class="btn btn-primary btn-sm" title="Generate PDF">
                                    <i class="bi bi-file-pdf-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-center">
                <a href="ajouter_commande.php?client_id=<?= $clientId ?>" class="btn btn-primary">Ajouter Une Commande</a>
            </div>

        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>
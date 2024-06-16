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

$servername = "localhost"; // Server name
$username = "root"; // Username
$password = ""; // Password
$dbname = "g_client";   // Database name
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (isset($_GET['search']) && !empty($_GET['search'])) { // Search
        $search = $_GET['search']; // Get search value
        $sql = "SELECT c.id, c.name, c.telephone, c.email, c.adresse, v.nom_ville AS ville 
                FROM client c
                JOIN ville v ON c.ville = v.id_ville
                WHERE c.name LIKE :search"; // SQL query
        $statement = $conn->prepare($sql);
        $statement->bindValue(':search', '%' . $search . '%');
    } else { // Get all
        $sql = "SELECT c.id, c.name, c.telephone, c.email, c.adresse, v.nom_ville AS ville
                FROM client c
                JOIN ville v ON c.ville = v.id_ville";
        $statement = $conn->prepare($sql);
    }


    $statement->execute();
    $client = $statement->fetchAll(PDO::FETCH_OBJ);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>Gestion Commerciale</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #fe3caa;
            background: -webkit-linear-gradient(0deg, #fe3caa 0%, #5394a6 100%);
            background: linear-gradient(0deg, #fe3caa 0%, #5394a6 100%);
        }

        .navbar {
            background: #5394a6;
            background: -webkit-linear-gradient(0deg, #3168a0 0%, #d977b5 100%);
            background: linear-gradient(0deg, #3168a0 0%, #d977b5 100%);
        }

        .bg-dark {
            background-color: #6b88a7 !important;
        }

        .navbar-brand {
            color: #fff;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .navbar-brand img {
            width: 40px;
            /* Increase the width for a bigger logo */
        }

        .form-control-dark {
            background-color: #333;
            color: #fff;
            border-color: #333;
        }

        .table-container {
            margin-top: 20px;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
        }

        .table {
            color: #333;
        }

        .btn-primary {
            background-color: #084298;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-danger {
            background-color: #8c1c03;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #dc3545;
            border-color: #bd2130;
        }

        .btn-secondary {
            color: #fff;
            background-color: #41464b;
            border-color: #6c757d;
        }

        .btn-success {
            background-color: #0f5132;
            border-color: #000;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>

<body>

    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <img src="logo.png" alt="Logo" width="60" height="60"> <!-- Increase the width and height for a bigger logo -->
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="menu.php" class="nav-link px-2 text-white">Menu</a></li>
                    <li><a href="all_commandes.php" class="nav-link px-2 text-white">Commandes</a></li>
                    <li><a href="all_produits.php" class="nav-link px-2 text-white">Produits</a></li>
                </ul>

                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" action="index.php" method="GET">
                    <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search" name="search">
                </form>
            </div>
        </div>
    </header>
    <div class="container table-container">
        <div class="row">
            <div class="col-10">
                <h5>Clients:</h5>
            </div>
            <div class="col-2 text-end">
                <a class="btn btn-success" href="ajouter.php"><small>+ Nouveau client</small></a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom client</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Adresse</th>
                        <th>Ville</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($client as $value) : ?>
                        <tr>
                            <td><?php echo $value->id ?></td>
                            <td><?php echo $value->name ?></td>
                            <td><?php echo $value->telephone ?></td>
                            <td><?php echo $value->email ?></td>
                            <td><?php echo $value->adresse ?></td>
                            <td><?php echo $value->ville ?></td>
                            <td>
                                <a href="modifier.php?id=<?= $value->id ?>" class="btn btn-primary btn-sm" title="Modifier">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="supprimer.php?id=<?= $value->id ?>" class="btn btn-danger btn-sm" title="Supprimer">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                                <a href="commande.php?id=<?= $value->id ?>" class="btn btn-secondary btn-sm" title="Commandes">
                                    <i class="bi bi-cart-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>
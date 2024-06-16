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

$errorMsg = isset($_GET['errorMsg']) ? $_GET['errorMsg'] : "";
$successMsg = isset($_GET['successMsg']) ? $_GET['successMsg'] : "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pagination variables
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $perPage = 10; // Number of products per page
    $offset = ($currentPage - 1) * $perPage;

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $sql = "SELECT id_produit, nom_produit, prix_uni, quantite
                FROM articles
                WHERE nom_produit LIKE :search
                LIMIT :offset, :perPage";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':search', '%' . $search . '%');
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    } else {
        $sql = "SELECT id_produit, nom_produit, prix_uni, quantite
                FROM articles
                LIMIT :offset, :perPage";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    }

    $statement->execute();
    $commands = $statement->fetchAll(PDO::FETCH_OBJ);

    $totalProducts = count($commands);
    $totalPages = ceil($totalProducts / $perPage);
} catch (PDOException $e) {
    $errorMsg = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $errorMsg = "An error occurred: " . $e->getMessage();
}
$conn = null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css" rel="stylesheet">
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

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .bg-dark {
            background-color: #6b88a7 !important;
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

        .table-container table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-container {
            margin-top: 15px;
        }

        .table-actions {
            white-space: nowrap;
        }

        .error-button {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .error-button:hover {
            background-color: #c82333;
            border-color: #bd2130;
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
                    <li><a href="all_commandes.php" class="nav-link px-2 text-white">Commandes</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container my-5">
        <?php if (!empty($errorMsg)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>


        <?php if (!empty($successMsg)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMsg; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-lg-6">
                <h5>Produits Disponibles:</h5>
            </div>
            <div class="col-12 col-lg-6">
                <form class="mb-3 mb-lg-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Rechercher un produit..." name="search">
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($commands)) : ?>
            <div class="alert alert-info mt-3" role="alert">
                Pas de produits.
            </div>
            <a href="ajouter_produit.php" class="btn btn-primary">Ajouter un produit</a>
        <?php else : ?>
            <div class="table-container">
                <table class="table table-striped table-hover mt-3">
                    <thead>
                        <tr>
                            <th>Produit ID</th>
                            <th>Produit Name</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th class="table-actions">Actions</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commands as $command) : ?>
                            <tr>
                                <td><?= $command->id_produit ?></td>
                                <td><?= $command->nom_produit ?></td>
                                <td><?= $command->quantite ?></td>
                                <td><?= $command->prix_uni ?> DH</td>
                                <td class="table-actions">
                                    <a href="supprimer_produit.php?id=<?= $command->id_produit ?>" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                                    <a href="modifier_produit.php?id=<?= $command->id_produit ?>" class="btn btn-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#barcodeModal<?= $command->id_produit ?>"><i class="bi bi-upc"></i></button>
                                    <!-- Barcode Modal -->
                                    <div class="modal fade" id="barcodeModal<?= $command->id_produit ?>" tabindex="-1" aria-labelledby="barcodeModalLabel<?= $command->id_produit ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="barcodeModalLabel<?= $command->id_produit ?>">Barcode for <?= $command->nom_produit ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <!-- Place your barcode display code here -->
                                                    <img src="barcode_generator.php?id=<?= $command->id_produit ?>" alt="Barcode for <?= $command->nom_produit ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="details_produit.php?id=<?= $command->id_produit ?>" class="btn btn-success btn-sm"><i class="bi bi-info-square-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <a href="ajouter_produit.php" class="btn btn-primary">Ajouter un produit</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoL2vC2Rqb6rIKBQWNvDpmo3ODK5XUw0JFY3U2wpo6xIz2zZzsrrQ8A7z/2ERr" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw3W2HD9IQm4k1OK9g/sWd9MtwqTD11L7tp86vHcOprjxioV4g75iZzU9+ZqAs" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle click on the barcode button
            $(".view-barcode-btn").click(function() {
                // Get the product ID from the data attribute
                var productId = $(this).data("product-id");

                // Send AJAX request to get the barcode content
                $.ajax({
                    url: "get_barcode.php",
                    type: "GET",
                    data: {
                        product_id: productId
                    },
                    success: function(data) {
                        // Update the barcode content in the modal
                        $("#barcodeContent").html(data);

                        // Show the modal
                        $("#barcodeModal").modal("show");

                        // Send the barcode content to the Android app
                        // Modify this part to send the data to your Android app
                        // For example, you can use JavaScript Bridge or other communication methods.
                        // Here, we're just displaying the barcode content as an alert.
                        alert("Barcode Content: " + data);
                    },
                    error: function() {
                        alert("Error occurred while fetching barcode data.");
                    }
                });
            });
        });
    </script>
</body>

</html>
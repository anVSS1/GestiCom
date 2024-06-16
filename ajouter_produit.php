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

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "g_client";

    // Create a connection to the MySQL database
    $conn = new mysqli($servername, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the form data
    $product_name = $_POST['nom_produit'];
    $price = $_POST['prix_uni'];
    $quantity = $_POST['quantite'];
    $description = $_POST['description'];

    // Image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'images/';
        $fileExtension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $uniqueFilename = uniqid() . '.' . $fileExtension;
        $targetFile = $targetDir . $uniqueFilename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
            // File upload successful, insert product details into the database
            $sql = "INSERT INTO articles (nom_produit, prix_uni, quantite, description, image_url) VALUES ('$product_name', '$price', '$quantity', '$description', '$uniqueFilename')";

            if ($conn->query($sql) === TRUE) {
                // Redirect to produits.php or display success message
                header("Location: all_produits.php");
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error uploading the image.";
        }
    }

    // Close the database connection
    $conn->close();
    exit; // Add exit here to prevent further code execution
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Ajouter un produit</title>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="text-center">Ajouter un nouveau produit</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nom_produit" class="form-label">Nom du produit</label>
                                <input type="text" class="form-control" id="nom_produit" name="nom_produit" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="quantite" class="form-label">Quantité</label>
                                <input type="text" class="form-control" id="quantite" name="quantite" required>
                            </div>
                            <div class="mb-3">
                                <label for="prix_uni" class="form-label">Prix</label>
                                <input type="text" class="form-control" id="prix_uni" name="prix_uni" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="product_image" name="product_image" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="submit" class="btn btn-primary">Ajouter</button>
                                <a href="index.php" class="btn btn-secondary ml-2">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
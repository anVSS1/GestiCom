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
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$products = []; // Create an empty array to store products

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commande_id = $_GET['id'];
    $product_name = $_POST['nom_produit'];
    $quantity = $_POST['quantite'];

    // Check if there is enough stock for the selected product
    $productSql = "SELECT id_produit, prix_uni, quantite AS stock FROM articles WHERE nom_produit = :product_name";
    $productStatement = $conn->prepare($productSql);
    $productStatement->bindValue(':product_name', $product_name);
    $productStatement->execute();
    $productRow = $productStatement->fetch(PDO::FETCH_ASSOC);

    if ($productRow !== false) {
        $product_id = $productRow['id_produit'];
        $unit_price = $productRow['prix_uni'];
        $current_stock = $productRow['stock'];

        if ($quantity <= $current_stock) {
            try {
                // Update the stock in the articles table
                $new_stock = $current_stock - $quantity;
                $updateStockSql = "UPDATE articles SET quantite = :new_stock WHERE id_produit = :product_id";
                $updateStockStatement = $conn->prepare($updateStockSql);
                $updateStockStatement->bindValue(':new_stock', $new_stock);
                $updateStockStatement->bindValue(':product_id', $product_id);
                $updateStockStatement->execute();

                // Insert the product details into the details table
                $sql = "INSERT INTO details (id_commande, id_produit, nom_produit, quantite, prix_unitaire) VALUES (:commande_id, :product_id, :product_name, :quantity, :price)";
                $statement = $conn->prepare($sql);
                $statement->bindValue(':commande_id', $commande_id);
                $statement->bindValue(':product_id', $product_id);
                $statement->bindValue(':product_name', $product_name);
                $statement->bindValue(':quantity', $quantity);
                $statement->bindValue(':price', $unit_price);
                $statement->execute();

                // Redirect back to the details.php page after successful insertion
                header("Location: details.php?id=$commande_id");
                exit();
            } catch (PDOException $e) {
                $sql = ""; // Declare an empty $sql variable in case of an error
                echo $sql . "<br>" . $e->getMessage();
            }
        } else {
            echo "Not enough stock for the selected product.";
        }
    } else {
        echo "Product not found in the database.";
    }
}

// Retrieve products from the articles table
$productsSql = "SELECT nom_produit, id_produit, prix_uni FROM articles";
$productsStatement = $conn->query($productsSql);
while ($productRow = $productsStatement->fetch(PDO::FETCH_ASSOC)) {
    $products[] = $productRow; // Add each product to the products array
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Ajouter les détails de la commande</title>
</head>

<body>
    <div class="container my-5">
        <h5>Ajouter details de commande</h5>
        <script>
            function updateFields() {
                var selectedProduct = document.getElementById("nom_produit").value;
                var productIdInput = document.getElementById("id_produit");
                var unitPriceInput = document.getElementById("prix_uni");
                var options = document.getElementById("nom_produit").options;

                for (var i = 0; i < options.length; i++) {
                    var option = options[i];
                    if (option.value === selectedProduct) {
                        productIdInput.value = option.getAttribute("data-product-id");
                        unitPriceInput.value = option.getAttribute("data-unit-price");
                        break;
                    }
                }
            }
        </script>
        <form method="POST">
            <div class="mb-3">
                <label for="nom_produit" class="form-label">Nom du produit</label>
                <select class="form-select" id="nom_produit" name="nom_produit" onchange="updateFields()" required>
                    <?php
                    // Loop through the products array to display the options
                    foreach ($products as $product) {
                        $productName = $product['nom_produit'];
                        echo "<option value='$productName' data-product-id='{$product['id_produit']}' data-unit-price='{$product['prix_uni']}'>$productName</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_produit" class="form-label">ID du produit</label>
                <input type="text" class="form-control" id="id_produit" name="id_produit" readonly required>
            </div>
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité</label>
                <input type="text" class="form-control" id="quantite" name="quantite" required>
            </div>
            <div class="mb-3">
                <label for="prix_uni" class="form-label">Prix unitaire</label>
                <input type="text" class="form-control" id="prix_uni" name="prix_uni" readonly required>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="details.php?id=<?php echo $_GET['id']; ?>" class="btn btn-secondary ml-2">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
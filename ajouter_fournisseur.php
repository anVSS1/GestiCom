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

$name = $address = $telephone = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $telephone = $_POST["telephone"];
    $email = $_POST["email"];

    $sql = "INSERT INTO fournisseurs (nom, adresse, telephone, email) VALUES (:nom, :adresse, :telephone, :email)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":nom", $name);
    $stmt->bindParam(":adresse", $address);
    $stmt->bindParam(":telephone", $telephone);
    $stmt->bindParam(":email", $email);

    if ($stmt->execute()) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Le fournisseur a été ajouté avec succès.'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Une erreur s\'est produite lors de l\'ajout du fournisseur.'
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
    <title>Ajouter un Fournisseur</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Ajouter un Fournisseur</h2>
    <form action="" method="post">
        <div class="form-group">
            <label>Nom du Fournisseur</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="form-group">
            <label>Adresse</label>
            <input type="text" class="form-control" name="address" required>
        </div>
        <div class="form-group">
            <label>Téléphone</label>
            <input type="text" class="form-control" name="telephone" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter Fournisseur</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

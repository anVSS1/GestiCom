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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST["name"];
    $address = $_POST["address"];
    $telephone = $_POST["telephone"];
    $email = $_POST["email"];

    $sql = "UPDATE fournisseurs SET nom = :nom, adresse = :adresse, telephone = :telephone, email = :email WHERE ID = :id";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":nom", $name);
    $stmt->bindParam(":adresse", $address);
    $stmt->bindParam(":telephone", $telephone);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Le fournisseur a été modifié avec succès.'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Une erreur s\'est produite lors de la modification du fournisseur.'
        ];
    }

    header("Location: fournisseurs.php"); // Redirigez vers la liste des fournisseurs
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM fournisseurs WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $fournisseur = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header("Location: fournisseurs.php"); // Redirigez en cas d'absence d'ID
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Fournisseur</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre fichier de styles CSS -->
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Modifier un Fournisseur</h2>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $fournisseur['id']; ?>">
                <div class="form-group">
                    <label>Nom du Fournisseur</label>
                    <input type="text" class="form-control" name="name" value="<?php echo $fournisseur['nom']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" class="form-control" name="address" value="<?php echo $fournisseur['adresse']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" class="form-control" name="telephone" value="<?php echo $fournisseur['telephone']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $fournisseur['email']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Modifier Fournisseur</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

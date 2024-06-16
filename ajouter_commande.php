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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "g_client";
    $client_id = $_POST['client_id'];
    $date = $_POST['date'];
    $observation = $_POST['observation'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO commande (client_id, date, observation, status) VALUES (:client_id, :date, :observation, :status)";
        $statement = $conn->prepare($sql);
        $statement->bindValue(':client_id', $client_id);
        $statement->bindValue(':date', $date);
        $statement->bindValue(':observation', $observation);
        $statement->bindValue(':status', 'en cours');
        $statement->execute();

        // Get the ID of the inserted commande
        $commandeId = $conn->lastInsertId();

        // Redirect to the details.php page for the newly added commande
        header("Location: add_details.php?id=$commandeId");
        exit();
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;
} else {
    // Retrieve the client ID from the previous page
    $client_id = $_GET['client_id'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Ajouter une commande</title>
    <style>
        .btn-primary {
            color: #fff;
            background-color: #084298;
            border-color: #084298;
        }

        .btn-secondary {
            color: #fff;
            background-color: #41464b;
            border-color: #41464b;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h5>Ajouter commande</h5>
        <form method="POST">
            <div class="mb-3">
                <label for="client_id" class="form-label">Client ID</label>
                <input type="text" class="form-control" id="client_id" name="client_id" value="<?php echo $client_id; ?>" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="observation" class="form-label">Observation</label>
                <textarea class="form-control" id="observation" name="observation" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="index.php" class="btn btn-secondary ml-2">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
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
if(isset($_POST["id"]) && !empty($_POST["id"])){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "g_client";
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    // DELETE
            
    $sql = "DELETE FROM client WHERE id = :id" ;
    
    if($stmt = $conn->prepare($sql)){

        $stmt->bindParam(":id", $param_id);
        
        $param_id = $_POST["id"];
        
        if($stmt->execute()){
            header("location: index.php");
            exit();
        } else{
            echo "error.";
        }
    }
     
    $stmt = null;
    
    $conn = null;
} else{
    if(empty($_GET["id"])){
        header("location: index.php");
        exit();
    }
}
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supprimer le client</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            max-width: 400px;
            margin: 0 auto;
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form action="" method="post">
                        <div class="alert alert-danger text-center">
                            <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>"/>
                            <p>Voulez-vous vraiment supprimer ce client ?</p>
                            <p>
                                <input type="submit" value="Oui" class="btn btn-danger">
                                <a href="index.php" class="btn btn-secondary ml-2">Non</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
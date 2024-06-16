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
try{
  $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$name = $telephone = $email = $adresse = $ville = "";
$name_err = $telephone_err = $email_err = $adresse_err = $ville_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_name = $_POST["name"];
    if (empty($input_name)) {
        $name_err = "Entrez Votre Nom:";
    } else {
        $name = $input_name;
    }

    // Validate telephone number
    $input_telephone = $_POST["telephone"];
    if(empty($input_telephone)){
        $telephone_err = "Entrez Votre Numero De Telephone:";     
    } else{
        $telephone = $input_telephone;
    }
    // Validate email address 
    $input_email = $_POST["email"];
    if(empty($input_email)){
        $email_err = "Entrez votre email.";     
    } else{
        $email = $input_email;
    }
    // Validate adresse 
    $input_adresse = $_POST["adresse"];
    if(empty($input_adresse)){
        $adresse_err = "Entrez votre adresse.";     
    } else{
        $adresse = $input_adresse;
    }
    // Validate ville
    $input_ville = $_POST["nom_ville"];
    if(empty($input_ville)){
        $ville_err = "Entrez votre ville.";     
    } else{
        $ville = $input_ville;
    }
    // CREATE
    if(empty($name_err)  && empty($telephone_err) && empty($email_err) && empty($adresse_err) && empty($ville_err)){

        $sql = "INSERT INTO client (name, telephone, email, adresse, ville ) VALUES (:name, :telephone, :email, :adresse, :ville)";
 
        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":name", $param_name);
            $stmt->bindParam(":telephone", $param_telephone);
            $stmt->bindParam(":email", $param_email);
            $stmt->bindParam(":adresse", $param_adresse);
            $stmt->bindParam(":ville", $param_ville);
            
            $param_name = $name;
            $param_telephone = $telephone;
            $param_email = $email;
            $param_adresse = $adresse;
            $param_ville = intval($ville);
        
            if($stmt->execute()){
                header("location: index.php");
                exit();
            } else{
                echo "error.";
            }
        }     
        // Close statement
        $stmt = null;
    }
    // Close connection
    $conn = null;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD GESTION CLIENT</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .bg-dark {
            background-color: #150926 !important;
        }
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<header class="p-3 bg-dark text-white">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="index.php" class="nav-link px-2 text-white">Home</a></li>
        </ul>

      </div>
    </div>
  </header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Nouveau Client</h2>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Nom de client</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Numero de telephone</label>
                            <input type="text" name="telephone" class="form-control <?php echo (!empty($telephone_err)) ? 'is-invalid' : ''; ?>"value="<?php echo $telephone;?>">
                            <span class="invalid-feedback"><?php echo $telephone_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Adresse Email</label>
                            <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="adresse" class="form-control <?php echo (!empty($adresse_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $adresse; ?>">
                            <span class="invalid-feedback"><?php echo $adresse_err;?></span>
                        </div>
                        <div class="form-group">
    <label>Ville</label>
    <select name="nom_ville" class="form-control <?php echo (!empty($ville_err)) ? 'is-invalid' : ''; ?>">
    <option value="">Sélectionnez une ville</option>
<?php
$sql = "SELECT id_ville, nom_ville FROM ville";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $selected = ($ville === $row['id_ville']) ? 'selected' : '';
    echo '<option value="' . $row['id_ville'] . '" ' . $selected . '>' . $row['nom_ville'] . '</option>';
}
?>
    </select>
    <span class="invalid-feedback"><?php echo $ville_err; ?></span>
</div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Annuler</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>
</html>
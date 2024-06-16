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
} catch(PDOException $e) { 
    echo "Connection failed: " . $e->getMessage();  
}

$name = $telephone = $email = $adresse = $ville = ""; 
$name_err = $telephone_err = $email_err = $adresse_err = $ville_err = ""; 


$villes = array(); 
try {
  $sql = "SELECT id_ville, nom_ville FROM ville"; 
  $stmt = $conn->query($sql); 
  $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);  
} catch(PDOException $e) { 
  echo "Error retrieving cities: " . $e->getMessage();  
}

if(isset($_POST["id"]) && !empty($_POST["id"])){ 
    $id = $_POST["id"]; 

    // Validate name 
    $input_name = $_POST["name"]; 
    if(empty($input_name)){ 
        $name_err = "Please enter your name:"; 
    } else{
        $name = $input_name; 
    }

    // Validate telephone number
    $input_telephone = $_POST["telephone"]; 
    if(empty($input_telephone)){ 
        $telephone_err = "Please enter your telephone number:";
    } else{
        $telephone = $input_telephone; 
    }

    // Validate email address
    $input_email = $_POST["email"]; 
    if(empty($input_email)){ 
        $email_err = "Please enter your email address:"; 
    } else{
        $email = $input_email; 
    }

    // Validate adresse
    $input_adresse = $_POST["adresse"]; 
    if(empty($input_adresse)){ 
        $adresse_err = "Please enter your adresse:";    
    } else{
        $adresse = $input_adresse; 
    }

    // Validate ville
    $input_ville = $_POST["nom_ville"]; 
    if(empty($input_ville)){ 
        $ville_err = "Please select a ville:";    
    } else{
        $ville = $input_ville; 
    }

    // UPDATE
    if(empty($name_err) && empty($telephone_err) && empty($email_err) && empty($adresse_err) && empty($ville_err)){ 
        $sql = "UPDATE client SET name=:name, telephone=:telephone, email=:email, adresse=:adresse, ville=:ville WHERE id=:id"; 
 
        if($stmt = $conn->prepare($sql)){ 
            $stmt->bindParam(":name", $param_name);
            $stmt->bindParam(":telephone", $param_telephone); 
            $stmt->bindParam(":email", $param_email);
            $stmt->bindParam(":adresse", $param_adresse); 
            $stmt->bindParam(":ville", $param_ville);
            $stmt->bindParam(":id", $param_id);
            
            $param_name = $name; 
            $param_telephone = $telephone; 
            $param_email = $email;
            $param_adresse = $adresse;
            $param_ville = intval($ville);
            $param_id = $id;
            
            if($stmt->execute()){ 
                header("location: index.php"); 
                exit(); 
            } else{
                echo "Error updating record.";
            }
        }
        // Close statement
        unset($stmt);
    }
    // Close connection
    unset($conn);
} 
else{
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){ 
        $id =  trim($_GET["id"]); 
        
        $sql = "SELECT * FROM client WHERE id = :id"; 

        if($stmt = $conn->prepare($sql)){ 
            $stmt->bindParam(":id", $param_id); 
            $param_id = $id;    
            if($stmt->execute()){   
                if($stmt->rowCount() == 1){ 
                    $row = $stmt->fetch(PDO::FETCH_ASSOC); 
                    $name = $row["name"]; 
                    $telephone = $row["telephone"]; 
                    $email = $row["email"];
                    $adresse = $row["adresse"];
                    $ville = $row["ville"];
                } else{
                    header("location: index.php"); 
                    exit();
                }
            } else{
                echo "Error retrieving record."; 
            }
        }
        // Close statement
        $stmt = null;
        // Close connection
        $conn = null;
    }  else{
        header("location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
            max-width: 500px;
            margin: 0 auto;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container wrapper">
        <h2 class="mt-4 mb-4">Modifier</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err;?></span>
            </div>
            <div class="form-group">
                <label for="telephone">Telephone:</label>
                <input type="text" name="telephone" id="telephone" class="form-control <?php echo (!empty($telephone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $telephone; ?>">
                <span class="invalid-feedback"><?php echo $telephone_err;?></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err;?></span>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" name="adresse" id="adresse" class="form-control <?php echo (!empty($adresse_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $adresse; ?>">
                <span class="invalid-feedback"><?php echo $adresse_err;?></span>
            </div>
            <div class="form-group">
                <label for="ville">Ville:</label>
                <select name="nom_ville" id="ville" class="form-control <?php echo (!empty($ville_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a ville</option>
                    <?php
                    foreach ($villes as $ville) {
                        if ($ville['nom_ville'] == $ville) {
                            echo '<option value="' . $ville['id_ville'] . '" selected>' . $ville['nom_ville'] . '</option>';
                        } else {
                            echo '<option value="' . $ville['id_ville'] . '">' . $ville['nom_ville'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <span class="invalid-feedback"><?php echo $ville_err;?></span>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <div class="form-group mt-4">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
            </div>
        </form>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

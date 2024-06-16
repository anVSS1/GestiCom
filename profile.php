<?php
// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: welcome.php");
    exit();
}

// Replace with your MySQL database credentials
$servername = "localhost";
$username = "root";
$password = ""; // If you have a password, add it here
$dbname = "g_client";

// Function to retrieve user data from MySQL database
function getUserData($email)
{
    global $servername, $username, $password, $dbname;

    // Create a connection to the MySQL database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement to retrieve user data
    $sql = "SELECT * FROM users WHERE email = ?";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);

    // Bind the email parameter to the prepared statement
    $stmt->bind_param("s", $email);

    // Execute the prepared statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch the user data from the result set
    $userData = $result->fetch_assoc();

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();

    return $userData;
}

// Function to update user profile in the database
function updateProfile($email, $name, $age, $localisation, $birthday, $phone_number)
{
    global $servername, $username, $password, $dbname;

    // Create a connection to the MySQL database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement to update user profile
    $sql = "UPDATE users SET name = ?, age = ?, localisation = ?, birthday = ?, telephone = ? WHERE email = ?";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters to the prepared statement
    $stmt->bind_param("ssssss", $name, $age, $localisation, $birthday, $phone_number, $email);

    // Execute the prepared statement
    $stmt->execute();

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the logged-in user's email from the session
    $loggedInUser = $_SESSION['email'];

    // Get the submitted form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $localisation = $_POST['localisation'];
    $birthday = $_POST['birthday'];
    $telephone = $_POST['telephone'];

    // Update the user profile
    updateProfile($loggedInUser, $name, $age, $localisation, $birthday, $telephone);

    // After updating the profile, retrieve the updated user data
    $userData = getUserData($loggedInUser);
} else {
    // If the form is not submitted, retrieve the user data from the session
    $loggedInUser = $_SESSION['email'];
    $userData = getUserData($loggedInUser);

    // Update last_login_time for the current user upon loading the page
    // Assuming $loggedInUser contains the email of the logged-in user
    $currentTime = date('Y-m-d H:i:s');
    $updateQuery = "UPDATE users SET last_login_time = '$currentTime' WHERE email = '$loggedInUser'";

    // Create a connection to the MySQL database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Execute the update query
    $conn->query($updateQuery);

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profil d'utilisateur</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #27272c;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        .profile-info {
            text-align: center;
        }

        .profile-info h1 {
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: bold;
        }

        .profile-info p {
            margin-bottom: 5px;
            font-size: 16px;
        }

        .profile-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        label {
            font-size: 16px;
            color: #ddd;
        }

        input[type="text"],
        input[type="date"] {
            width: 250px;
            padding: 8px;
            border: 2px solid #3cb371;
            border-radius: 5px;
            font-size: 14px;
            background-color: #333;
            color: #fff;
        }

        .update-btn {
            padding: 10px 20px;
            background-color: #3cb371;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .update-btn:hover {
            background-color: #2e8b57;
        }

        .profile-link,
        .menu-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF0000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            transition: background-color 0.3s ease;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: bold;
        }

        .profile-link:hover,
        .menu-link:hover {
            background-color: #3cb371;
        }
    </style>
</head>

<body>
    <div class="profile-info">
        <h1>Bienvenue, <?php echo $loggedInUser; ?></h1>
        <p>Voici vos détails de profil :</p>
        <p>Créé le : <?php echo $userData['creation_time']; ?></p>
        <p>Dernière connexion : <?php echo $userData['last_login_time']; ?></p>
    </div>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="profile-form">
        <label for="name">Nom :</label>
        <input type="text" id="name" name="name" value="<?php echo $userData['name']; ?>">

        <label for="age">Âge :</label>
        <input type="text" id="age" name="age" value="<?php echo $userData['age']; ?>">

        <label for="localisation">Localisation :</label>
        <input type="text" id="localisation" name="localisation" value="<?php echo $userData['localisation']; ?>">

        <label for="birthday">Date de naissance :</label>
        <input type="date" id="birthday" name="birthday" value="<?php echo $userData['birthday']; ?>">

        <label for="phone_number">Numéro de téléphone :</label>
        <input type="text" id="phone_number" name="telephone" value="<?php echo $userData['telephone']; ?>">

        <input type="submit" value="Mettre à jour" class="update-btn">
    </form>

    <a href="logout.php" class="profile-link">Déconnexion</a>
    <a href="menu.php" class="menu-link">Menu</a>
</body>

</html>
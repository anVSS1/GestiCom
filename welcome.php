<?php
$error_message = '';
$error_message = '';
// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'g_client';

// Create a connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check if the connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Start a session
session_start();

// Handle sign up form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Validate and sanitize the username
  $username = trim($username); // Remove leading/trailing whitespace
  $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH); // Remove HTML tags and disallow low/high ASCII characters

  // Check if the username meets your specific requirements
  if (strlen($username) < 3) {
    $errorr_message = 'Le nom d\'utilisateur doit comporter au moins 3 caractères.';
  } elseif (strlen($username) > 50) {
    $errorr_message = 'Le nom d\'utilisateur ne peut pas dépasser 50 caractères.';
  } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errorr_message = 'Le nom d\'utilisateur ne peut contenir que des lettres, des chiffres et des tirets bas.';
  } else {
    // Check if the username already exists in the database
    $existingUserQuery = "SELECT * FROM users WHERE username = '$username'";
    $existingUserResult = mysqli_query($conn, $existingUserQuery);

    if (mysqli_num_rows($existingUserResult) > 0) {
      $errorrr_message = 'Le nom d\'utilisateur est déjà pris.';
    } else {
      // Generate a random salt
      $salt = bin2hex(random_bytes(16));

      // Hash the password with the salt
      $hashedPassword = password_hash($password . $salt, PASSWORD_DEFAULT);

      // Example query to insert data into a table
      $sql = "INSERT INTO users (username, email, password, salt) VALUES ('$username', '$email', '$hashedPassword', '$salt')";

      if (mysqli_query($conn, $sql)) {
        // Registration successful, redirect to success page or display success message
        header("Location: success.php");
        exit();
      } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
      }
    }
  }
}


// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Retrieve the user's salt from the database
  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $salt = $row['salt'];

    // Hash the entered password with the retrieved salt
    $hashedPassword = password_hash($password . $salt, PASSWORD_DEFAULT);

    // Compare the hashed passwords
    if (password_verify($password . $salt, $row['password'])) {
      // Store user data in the session
      $_SESSION['id'] = $row['id'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['username'] = $row['username'];

      // Redirect to the welcome page
      header("Location: menu.php");
      exit();
    }
  }

  // If login is unsuccessful, set an error message
  $error_message = 'Email ou mot de passe incorrect.';
}

// Close the connection 
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Bienvenue</title>
  <link rel="stylesheet" type="text/css" href="slide-navbar-style.css">
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: 'Jost', sans-serif;
      background: linear-gradient(to bottom, #04ccee, #04ccee, #04ccee);
    }

    .main {
      width: 350px;
      height: 500px;
      background: red;
      overflow: hidden;
      background: url("https://doc-08-2c-docs.googleusercontent.com/docs/securesc/68c90smiglihng9534mvqmq1946dmis5/fo0picsp1nhiucmc0l25s29respgpr4j/1631524275000/03522360960922298374/03522360960922298374/1Sx0jhdpEpnNIydS4rnN4kHSJtU1EyWka?e=view&authuser=0&nonce=gcrocepgbb17m&user=03522360960922298374&hash=tfhgbs86ka6divo3llbvp93mg4csvb38") no-repeat center/ cover;
      border-radius: 10px;
      box-shadow: 5px 20px 50px #000;
    }

    #chk {
      display: none;
    }

    .signup {
      position: relative;
      width: 100%;
      height: 100%;
    }

    label {
      color: #fff;
      font-size: 2.3em;
      justify-content: center;
      display: flex;
      margin: 60px;
      font-weight: bold;
      cursor: pointer;
      transition: .5s ease-in-out;
    }

    input {
      width: 60%;
      height: 20px;
      background: #e0dede;
      justify-content: center;
      display: flex;
      margin: 20px auto;
      padding: 10px;
      border: none;
      outline: none;
      border-radius: 5px;
    }

    button {
      width: 60%;
      height: 40px;
      margin: 10px auto;
      justify-content: center;
      display: block;
      color: #fff;
      background: #115d6a;
      font-size: 1em;
      font-weight: bold;
      margin-top: 20px;
      outline: none;
      border: none;
      border-radius: 5px;
      transition: .2s ease-in;
      cursor: pointer;
    }

    button:hover {
      background: #04ccee;
    }

    .login {
      height: 460px;
      background: #eee;
      border-radius: 60% / 10%;
      transform: translateY(-180px);
      transition: .8s ease-in-out;
    }

    .login label {
      color: #573b8a;
      transform: scale(.6);
    }

    #chk:checked~.login {
      transform: translateY(-500px);
    }

    #chk:checked~.login label {
      transform: scale(1);
    }

    #chk:checked~.signup label {
      transform: scale(.6);
    }

    .main .error-message {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<!DOCTYPE html>
<html>

<head>
  <title>Bienvenue</title>
  <link rel="stylesheet" type="text/css" href="slide-navbar-style.css">
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: 'Jost', sans-serif;
      background: linear-gradient(to bottom, #04ccee, #04ccee, #04ccee);
    }

    .main {
      width: 350px;
      height: 500px;
      background: red;
      overflow: hidden;
      background: url("https://doc-08-2c-docs.googleusercontent.com/docs/securesc/68c90smiglihng9534mvqmq1946dmis5/fo0picsp1nhiucmc0l25s29respgpr4j/1631524275000/03522360960922298374/03522360960922298374/1Sx0jhdpEpnNIydS4rnN4kHSJtU1EyWka?e=view&authuser=0&nonce=gcrocepgbb17m&user=03522360960922298374&hash=tfhgbs86ka6divo3llbvp93mg4csvb38") no-repeat center/ cover;
      border-radius: 10px;
      box-shadow: 5px 20px 50px #000;
    }

    #chk {
      display: none;
    }

    .signup {
      position: relative;
      width: 100%;
      height: 100%;
    }

    label {
      color: #fff;
      font-size: 2.3em;
      justify-content: center;
      display: flex;
      margin: 60px;
      font-weight: bold;
      cursor: pointer;
      transition: .5s ease-in-out;
    }

    input {
      width: 60%;
      height: 20px;
      background: #e0dede;
      justify-content: center;
      display: flex;
      margin: 20px auto;
      padding: 10px;
      border: none;
      outline: none;
      border-radius: 5px;
    }

    button {
      width: 60%;
      height: 40px;
      margin: 10px auto;
      justify-content: center;
      display: block;
      color: #fff;
      background: #115d6a;
      font-size: 1em;
      font-weight: bold;
      margin-top: 20px;
      outline: none;
      border: none;
      border-radius: 5px;
      transition: .2s ease-in;
      cursor: pointer;
    }

    button:hover {
      background: #04ccee;
    }

    .login {
      height: 460px;
      background: #eee;
      border-radius: 60% / 10%;
      transform: translateY(-180px);
      transition: .8s ease-in-out;
    }

    .login label {
      color: #573b8a;
      transform: scale(.6);
    }

    #chk:checked~.login {
      transform: translateY(-500px);
    }

    #chk:checked~.login label {
      transform: scale(1);
    }

    #chk:checked~.signup label {
      transform: scale(.6);
    }

    .main .error-message {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="main">
    <input type="checkbox" id="chk" aria-hidden="true">

    <div class="signup">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="chk" aria-hidden="true">Sign up</label>
        <input type="text" name="username" placeholder="User name" required="">
        <input type="email" name="email" placeholder="Email" required="">
        <input type="password" name="password" placeholder="Password" required="">
        <button type="submit">Sign up</button>
        <?php if (isset($errorr_message)) {
          echo '<div class="error-message">' . $errorr_message . '</div>';
        } ?>
        <?php if (isset($errorrr_message)) {
          echo '<div class="error-message">' . $errorrr_message . '</div>';
        } ?>
      </form>
    </div>

    <div class="login">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="chk" aria-hidden="true">Login</label>
        <input type="email" name="email" placeholder="Email" required="">
        <input type="password" name="password" placeholder="Password" required="">
        <button type="submit">Login</button>
        <?php if (isset($error_message)) {
          echo '<div class="error-message">' . $error_message . '</div>';
        } ?>
      </form>
    </div>
  </div>

</body>



</html>
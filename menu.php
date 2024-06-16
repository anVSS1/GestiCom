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
$host = "localhost";
$username = "root";
$password = "";
$database = "g_client";
$connection = mysqli_connect($host, $username, $password, $database);
if (!$connection) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}

$commandsData = array();
$sql = "SELECT YEAR(date) AS year, COUNT(*) AS commands_count FROM commande GROUP BY year";
$result = mysqli_query($connection, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $commandsData[] = array(
            "Year" => $row["year"],
            "Commands" => $row["commands_count"]
        );
    }
}

// Get the number of products
$sql = "SELECT COUNT(*) AS articles_count FROM articles";
$result = mysqli_query($connection, $sql);
$productCount = mysqli_fetch_assoc($result)['articles_count'];

// Get the number of commands
$sql = "SELECT COUNT(*) AS commandes_count FROM commande";
$result = mysqli_query($connection, $sql);
$commandCount = mysqli_fetch_assoc($result)['commandes_count'];

// Get the number of clients
$sql = "SELECT COUNT(*) AS clients_count FROM client";
$result = mysqli_query($connection, $sql);
$clientCount = mysqli_fetch_assoc($result)['clients_count'];

// Close the database connection
mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        html,
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #fe3caa;
            background: -webkit-linear-gradient(0deg, #fe3caa 0%, #5394a6 100%);
            background: linear-gradient(0deg, #fe3caa 0%, #5394a6 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        #sidebar {
            width: 200px;
            height: 100%;
            background: #1a1a1a;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            transition: 0.5s;
            z-index: 999;
            padding-bottom: 60px;
        }

        #sidebar-top {
            color: #fff;
            padding: 10px;
            font-size: 18px;
            background-color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
        }

        #sidebar-content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            height: calc(100% - 80px);
            padding-left: 20px;
            padding-top: 40px;
        }

        #sidebar-content a {
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: #fff;
            text-decoration: none;
            width: 100%;
            padding-left: 10px;
        }

        #sidebar-content i {
            font-size: 20px;
            margin-right: 10px;
            margin-bottom: 5px;
        }

        #sidebar-content .icon-clients {
            color: #39FF14;
            font-size: 20px;
        }

        #sidebar-content .icon-commandes {
            color: #ffd600;
            font-size: 20px;
        }

        #sidebar-content .icon-produits {
            color: #0FF0FC;
            font-size: 20px;
        }

        #sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px;
            background-color: #1a1a1a;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .profile-button {
            display: inline-block;
            padding: 8px 20px;
            background-color: #116D6E;
            color: white;
            border: none;
            border-radius: 4px;
            transition-duration: 0.4s;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .profile-info {
            text-align: center;
            margin-bottom: 10px;
        }

        .profile-info h3 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .profile-button:hover {
            background-color: #45a049;
        }

        .profile-button span {
            display: block;
            margin-top: 5px;
        }


        #sidebar-footer p {
            margin-bottom: 10px;
        }

        #sidebar .closebtn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 36px;
            color: #fff;
            cursor: pointer;
        }

        #main {
            transition: margin-left 0.5s;
            padding: 16px;
            width: 80%;
            margin-left: 200px;
            text-align: center;
        }


        #graph-buttons {
            margin-bottom: 20px;
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            padding: 10px;
        }

        #logo {
            text-align: center;
            margin-bottom: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 10px;
        }


        #graph-buttons button {
            position: relative;
            background: #444;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            border: none;
            letter-spacing: 0.1rem;
            font-size: 1rem;
            padding: 1rem 3rem;
            transition: 0.2s;
        }

        #graph-buttons button:hover {
            letter-spacing: 0.2rem;
            padding: 1.1rem 3.1rem;
            background: var(--clr);
            color: var(--clr);
            animation: box 3s infinite;
        }

        #graph-buttons button::before {
            content: "";
            position: absolute;
            inset: 2px;
            background: #272822;
        }

        #graph-buttons button span {
            position: relative;
            z-index: 1;
        }

        #graph-buttons button i {
            position: absolute;
            inset: 0;
            display: block;
        }

        #graph-buttons button i::before {
            content: "";
            position: absolute;
            width: 10px;
            height: 2px;
            left: 80%;
            top: -2px;
            border: 2px solid var(--clr);
            background: #272822;
            transition: 0.2s;
        }

        #graph-buttons button:hover i::before {
            width: 15px;
            left: 20%;
            animation: move 3s infinite;
        }

        #graph-buttons button i::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 2px;
            left: 20%;
            bottom: -2px;
            border: 2px solid var(--clr);
            background: #272822;
            transition: 0.2s;
        }

        #graph-buttons button:hover i::after {
            width: 15px;
            left: 80%;
            animation: move 3s infinite;
        }

        @keyframes move {
            0% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(5px);
            }

            100% {
                transform: translateX(0);
            }
        }

        @keyframes box {
            0% {
                box-shadow: #27272c;
            }

            50% {
                box-shadow: 0 0 25px var(--clr);
            }

            100% {
                box-shadow: #27272c;
            }
        }

        #toggleButton {
            position: absolute;
            top: 10px;
            left: 210px;
            background: none;
            border: none;
            font-size: 24px;
            color: #fff;
            cursor: pointer;
        }


        #table {
            margin-top: 20px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            background-color: #1a1a1a;
            color: #fff;
            padding: 10px;
        }

        tbody td {
            background-color: #444;
            color: #fff;
            padding: 10px;
            word-break: break-all;
        }

        tbody tr:nth-child(even) td {
            background-color: #272822;
        }

        tbody tr:hover td {
            background-color: #ffd600;
            color: #000;
        }

        .mini-tables {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .table1 tbody tr:hover td {
            background-color: #0FF0FC;
        }

        .table2 tbody tr:hover td {
            background-color: #ffd600;
        }

        .table3 tbody tr:hover td {
            background-color: #39FF14;
        }

        .mini-table {
            flex-basis: calc(33.33% - 20px);
            margin-bottom: 20px;
        }

        .mini-table table {
            width: 100%;
        }

        .mini-table h3 {
            color: #fff;
            background-color: #1a1a1a;
            font-size: 17.4px;
            padding: 10px;
        }

        #footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #1a1a1a;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        #footer p {
            margin: 0;
            font-size: 12px;
        }
    </style>
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var main = document.getElementById("main");
            var button = document.getElementById("toggleButton");
            if (sidebar.style.display === "none") {
                sidebar.style.display = "block";
                main.style.marginLeft = "200px";
                button.style.left = "220px";
            } else {
                sidebar.style.display = "none";
                main.style.marginLeft = "0";
                button.style.left = "10px";
            }
        }
    </script>
</head>

<body>
    <div id="sidebar">
        <div id="sidebar-top">
            GestiCom
        </div>
        <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">×</a>
        <div id="sidebar-content">
            <i class="fas fa-users icon-clients"> Clients</i>
            <a href="index.php"><i class="fas fa-list icon-clients"></i> Tous les clients</a>
            <a href="ajouter.php"><i class="fas fa-plus-circle icon-clients"></i>Ajouter un client</a>
            <i class="fas fa-shopping-cart icon-commandes"> Commandes</i>
            <a href="all_commandes.php"><i class="fas fa-list icon-commandes"></i>Afficher toutes les Commandes</a>
            <a href="ajouter_commande.php"><i class="fas fa-plus-circle icon-commandes"></i>Ajouter une commande</a>
            <i class="fas fa-box icon-produits"> Produits </i>
            <a href="all_produits.php"><i class="fas fa-list icon-produits"></i>Afficher tous les produits</a>
            <a href="ajouter_produit.php"><i class="fas fa-plus-circle icon-produits"></i>Ajouter un produit</a>
        </div>

        <div id="sidebar-footer">
            <div class="profile-info">
                <h3>Welcome, <?php echo $loggedInUser; ?></h3>
            </div>
            <div class="profile-buttons-container">
                <a href="profile.php" class="profile-link">
                    <button class="profile-button">
                        <i class="fas fa-user"></i> <!-- Replace text with the user icon -->
                    </button>
                </a>
                <a href="logout.php" class="profile-link">
                    <button class="profile-button">
                        <i class="fas fa-sign-out-alt"></i> <!-- Replace text with the logout icon -->
                    </button>
                </a>
            </div>
        </div>
    </div>

    <div id="main">
        <div class="mini-tables">
            <div class="mini-table table3">
                <h3>Nombre de clients</h3>
                <table>
                    <tbody>
                        <tr>
                            <td><?php echo $clientCount; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mini-table table2">
                <h3>Nombre de Commandes</h3>
                <table>
                    <tbody>
                        <tr>
                            <td><?php echo $commandCount; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mini-table table1">
                <h3 style="color: #fff;">Nombre de produits</h3>
                <table>
                    <tbody>
                        <tr>
                            <td><?php echo $productCount; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="logo">
            <img src="logo.png" alt="Logo" style="width: 60px; height: 60px;">
        </div>
        <button id="toggleButton" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <div id="graph-buttons">
            <button style="--clr:#39FF14" onclick="window.location.href = 'index.php';"><span>Clients</span><i></i></button>
            <button style="--clr:#ffd600" onclick="window.location.href = 'all_commandes.php';"><span>Commandes</span><i></i></button>
            <button style="--clr:#0FF0FC" onclick="window.location.href = 'all_produits.php';"><span>Produits</span><i></i></button>
        </div>
        <div id="table">
            <table>
                <thead>
                    <tr>
                        <th>Annee</th>
                        <th>Commandes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandsData as $data) { ?>
                        <tr>
                            <td><?php echo $data["Year"]; ?></td>
                            <td><?php echo $data["Commands"]; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer id="footer">
        <p>&copy; 2023 GestiCom. All rights reserved. Developped by Kaoutar & Anass</p>
    </footer>

    <script>
        // JavaScript function to toggle the sidebar
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var main = document.getElementById("main");
            var button = document.getElementById("toggleButton");
            var logo = document.getElementById("logo");
            var graphButtons = document.getElementById("graph-buttons");
            var sidebarIcons = document.getElementById("sidebar-content").querySelectorAll("i");

            if (sidebar.style.display === "none") {
                sidebar.style.display = "block";
                main.style.marginLeft = "200px";
                button.style.left = "220px";
                logo.style.left = "220px";
                graphButtons.style.marginLeft = "220px";
                sidebarIcons.forEach(function(icon) {
                    icon.style.opacity = "1";
                });
            } else {
                sidebar.style.display = "none";
                main.style.marginLeft = "0";
                button.style.left = "10px";
                logo.style.left = "10px";
                graphButtons.style.marginLeft = "10px";
                sidebarIcons.forEach(function(icon) {
                    icon.style.opacity = "0";
                });
            }
        }

        // JavaScript function to logout the user
        function logout() {
            window.location.href = "logout.php";
        }
    </script>
</body>

</html>
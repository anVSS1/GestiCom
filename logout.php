<?php
// Start the session
session_start();

// Check if the session is active and the user is logged in
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['id'])) {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
}

// Redirect the user to the login page
header("Location: welcome.php");
exit;
?>

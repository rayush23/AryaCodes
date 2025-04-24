<?php
session_start(); // Start or resume the session

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Destroy all session data
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
}

// Redirect to the homepage after logout
header('Location: index.php');
exit();
?>


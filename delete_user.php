<?php
session_start();
require_once 'db_connection.php';

// Ensure only agents can access this page
if ($_SESSION['UserType'] !== 'Agent') {
    header("Location: index.php");
    exit();
}

// Check if UserID is passed in the URL
if (isset($_GET['UserID'])) {
    $user_id = $_GET['UserID'];

    // Delete user from the database
    $sql = "DELETE FROM User WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User deleted successfully!";
        header("Location: agent_profile.php#view_clients");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
} else {
    echo "No user selected.";
    exit();
}
?>

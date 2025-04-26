<?php
session_start();
require_once 'db_connection.php';

// Ensure only agents can access this page
if ($_SESSION['UserType'] !== 'Agent') {
    header("Location: index.php");
    exit();
}

// Check if PropertyID is passed in the URL
if (isset($_GET['PropertyID'])) {
    $property_id = $_GET['PropertyID'];

    // Delete the property from the database
    $sql = "DELETE FROM Property WHERE PropertyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);

    if ($stmt->execute()) {
        echo "Property deleted successfully!";
        header("Location: agent_profile.php#manage_properties");
        exit();
    } else {
        echo "Error deleting property: " . $conn->error;
    }
} else {
    echo "No property selected.";
    exit();
}
?>

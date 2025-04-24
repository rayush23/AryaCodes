<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT Name, Email, PhoneNumber, UserType FROM User WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account</title>
    <link rel="stylesheet" href="userAccount.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Braj Property</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="properties.php">Properties</a></li>
                <li><a href="agent.php">Agents</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="account-details">
        <h2>Your Account Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['Name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['PhoneNumber']); ?></p>
        <p><strong>Account Type:</strong> <?php echo htmlspecialchars($user['UserType']); ?></p>

        <!-- Link to edit account -->
        <a href="userAccountEdit.php">Edit Account Information</a>
    </div>

    <footer style="background-color: #222; color: #fff; padding: 20px; text-align: center;">
        <p>&copy; 2024 Braj Property. All Rights Reserved.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>

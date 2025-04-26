<?php include 'header.php'; 
if (!isLoggedIn()) { header('Location: login.php'); exit; }
?>
<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = $error_message = "";

// Fetch user details from the database
$sql = "SELECT Name, Email, PhoneNumber FROM User WHERE UserID = ?";
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

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Input validation
    if (empty($name) || empty($email) || empty($phone_number)) {
        $error_message = "All fields are required.";
    } else {
        // Prepare SQL to update user details
        $update_sql = "UPDATE User SET Name = ?, Email = ?, PhoneNumber = ? WHERE UserID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $phone_number, $user_id);
        
        if ($update_stmt->execute()) {
            // Update session with new name
            $_SESSION['username'] = $name;

            // Redirect back to view account page after successful update
            header('Location: userAccount.php');
            exit(); // Ensure no further script execution after header
        } else {
            $error_message = "There was an error updating your information. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Account</title>
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
                <li><a href="agents.php">Agents</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isLoggedIn() && isClient()): ?>
                  <li><a href="client_appointments.php">My Appointments</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="account-details">
        <h2>Edit Your Account Information</h2>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" required>

            <!-- UserType is not editable -->
            <p><strong>Account Type:</strong> <?php echo htmlspecialchars($_SESSION['UserType']); ?></p>

            <button type="submit">Update Account</button>
        </form>
    </div>

    <footer style="background-color: #222; color: #fff; padding: 20px; text-align: center;">
        <p>&copy; 2024 Braj Property. All Rights Reserved.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>

<?php
// signup.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'header.php';
require_once 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect & trim inputs
    $username    = trim($_POST['username']    ?? '');
    $email       = trim($_POST['email']       ?? '');
    $phone       = trim($_POST['phone_number']?? '');
    $user_type   = $_POST['user_type']        ?? 'Client';
    $plain       = $_POST['password']        ?? '';
    $confirm     = $_POST['confirm_password']?? '';

    // Validate inputs
    if (!$username || !$email || !$plain || !$phone) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($plain !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Hash password
        $hash = password_hash($plain, PASSWORD_DEFAULT);

        // Prepare insert statement
        $sql = "INSERT INTO `user` (Name, Email, Password, PhoneNumber, UserType)
                VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters: username, email, hashed password, phone, user type
            $stmt->bind_param('sssss', $username, $email, $hash, $phone, $user_type);
            if ($stmt->execute()) {
                $success = 'Registration successful! <a href="login.php">Log in now</a>.';
            } else {
                $error = 'Error registering user: ' . htmlspecialchars($stmt->error);
            }
            $stmt->close();
        } else {
            $error = 'Database error: ' . htmlspecialchars($conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up – Braj Property</title>
  <link rel="stylesheet" href="signup.css">
</head>
<body>
  <div class="register-container">
    <h2>Sign Up</h2>
    <?php if ($error): ?>
      <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($success): ?>
      <p class="success-message"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="post" action="signup.php">
      <label for="username">Name:</label>
      <input type="text" id="username" name="username" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="phone_number">Phone Number:</label>
      <input type="text" id="phone_number" name="phone_number" required>

      <label for="user_type">User Type:</label>
      <select id="user_type" name="user_type">
        <option value="Client">Client</option>
        <option value="Agent">Agent</option>
      </select>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <label for="confirm_password">Confirm Password:</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
  <footer>
    <p>&copy; <?php echo date('Y'); ?> Braj Property. All Rights Reserved.</p>
  </footer>
</body>
</html>

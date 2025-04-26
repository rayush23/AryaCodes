<?php include 'header.php'; ?>

<?php
// login.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// 1) Database connection (uses your existing db_connection.php)
require_once 'db_connection.php';

$error = '';
$email = '';

// 2) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        // 3) Look up the user (case‑insensitive email)
        $sql = "SELECT UserID, Name, Password, UserType
                FROM `user`
                WHERE LOWER(Email) = LOWER(?)
                LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $email);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result && $result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // 4) Verify the password hash
                    if (password_verify($password, $user['Password'])) {
                        // 5) Set session variables
                        $_SESSION['user_id']  = $user['UserID'];
                        $_SESSION['username'] = $user['Name'];
                        $_SESSION['UserType'] = $user['UserType'];  // Admin, Agent, or Client

                        // 6) Redirect based on role
                        if ($user['UserType'] === 'Admin') {
                            header('Location: admin_dashboard.php');
                        }
                        elseif ($user['UserType'] === 'Agent') {
                            header('Location: agent_profile.php');
                        }
                        else {
                            header('Location: userAccount.php');
                        }
                        exit;
                    } else {
                        $error = 'Invalid email or password.';
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                error_log('Login execute error: ' . $stmt->error);
                $error = 'An internal error occurred. Please try again later.';
            }
            $stmt->close();
        } else {
            error_log('Login prepare error: ' . $conn->error);
            $error = 'An internal error occurred. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login – Braj Property</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <form method="post" action="">
      <label for="email">Email:</label>
      <input
        type="email"
        id="email"
        name="email"
        required
        value="<?php echo htmlspecialchars($email); ?>"
      >

      <label for="password">Password:</label>
      <input
        type="password"
        id="password"
        name="password"
        required
      >

      <button type="submit">Login</button>

      <?php if ($error): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
  </div>

  <footer>
    <p>&copy; 2024 Braj Property. All Rights Reserved.</p>
    <div class="social-buttons">
      <a href="https://wa.me/" class="whatsapp" target="_blank">WhatsApp</a>
      <a href="https://facebook.com" class="facebook" target="_blank">Facebook</a>
    </div>
  </footer>
</body>
</html>

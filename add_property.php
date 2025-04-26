<?php
// add_property.php – Submit new property requests for admin approval

require_once 'db_connection.php';
include 'header.php';    // session + isAgent(), isLoggedIn()

// Guard: only logged-in Agents may access
if (!isLoggedIn() || !isAgent()) {
    header('Location: login.php');
    exit;
}

$error      = '';
$success    = '';
$name       = '';
$type       = '';
$location   = '';
$price      = '';
$size       = '';
$imagePath  = '';

// Handle form POST
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($isPost) {
    $name      = trim($_POST['property_name'] ?? '');
    $type      = trim($_POST['property_type'] ?? '');
    $location  = trim($_POST['location']      ?? '');
    $price     = trim($_POST['price']         ?? '');
    $size      = trim($_POST['size']          ?? '');

    // file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp       = $_FILES['image']['tmp_name'];
        $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/' . uniqid('prop_') . ".{$ext}";
        move_uploaded_file($tmp, $imagePath);
    } else {
        $imagePath = '';
    }

    // basic validation
    if (!$name || !$type || !$location || !is_numeric($price) || !is_numeric($size)) {
        $error = 'Please fill in all fields correctly.';
    }

    if (!$error) {
        // Build JSON payload for the request
        $payload = [
            'Name'     => $name,
            'Type'     => $type,
            'Location' => $location,
            'Price'    => (float)$price,
            'Size'     => (float)$size
        ];
        if ($imagePath) {
            $payload['Image'] = $imagePath;
        }
        $jsonPayload = json_encode($payload);

        // Insert into property_requests table as a JSON payload
        $agentId = $_SESSION['user_id'];
        $stmt    = $conn->prepare(
            "INSERT INTO property_requests (AgentID, Action, Payload) VALUES (?, 'ADD', ?)"
        );
        $stmt->bind_param('is', $agentId, $jsonPayload);
        if ($stmt->execute()) {
            $success  = 'Property request submitted successfully.';
            // clear fields
            $name = $type = $location = $price = $size = $imagePath = '';
        } else {
            $error = 'Failed to submit request: ' . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Request New Property &mdash; Braj Property</title>
  <link rel="stylesheet" href="about.css" type="text/css">
</head>
<body>
  <header>
    <div class="site-container">
      <a href="index.php" class="logo">Braj Property</a>
      <nav>
        <ul class="nav-list">
          <li><a href="index.php">Home</a></li>
          <li><a href="properties.php">Properties</a></li>
          <li><a href="agent_profile.php">Dashboard</a></li>
          <li><a href="add_property.php" class="active">New Property</a></li>
          <li><a href="agent_appointments.php">Appointments</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="about-section site-container">
    <h1>Request New Property</h1>

    <?php if ($isPost): ?>
      <?php if ($error): ?>
        <p style="color:red; margin-bottom:1rem;"><?= htmlspecialchars($error) ?></p>
      <?php elseif ($success): ?>
        <p style="color:green; margin-bottom:1rem;"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>
    <?php endif; ?>

    <form method="POST" action="add_property.php" enctype="multipart/form-data">
      <label for="property_name">Name:</label><br>
      <input type="text" id="property_name" name="property_name" value="<?= htmlspecialchars($name) ?>" required><br><br>

      <label for="property_type">Type:</label><br>
      <input type="text" id="property_type" name="property_type" value="<?= htmlspecialchars($type) ?>" required><br><br>

      <label for="location">Location:</label><br>
      <input type="text" id="location" name="location" value="<?= htmlspecialchars($location) ?>" required><br><br>

      <label for="price">Price (MUR):</label><br>
      <input type="number" id="price" name="price" value="<?= htmlspecialchars($price) ?>" required><br><br>

      <label for="size">Size (sqft):</label><br>
      <input type="number" id="size" name="size" value="<?= htmlspecialchars($size) ?>" required><br><br>

      <label for="image">Image:</label><br>
      <input type="file" id="image" name="image" accept="image/*"><br><br>

      <button type="submit">Submit Request</button>
    </form>
  </main>

  <footer>
    <div class="site-container">
      <p>&copy; <?= date('Y') ?> Braj Property. All Rights Reserved.</p>
      <div class="social-buttons">
        <a href="https://wa.me/" target="_blank">WhatsApp</a>
        <a href="https://facebook.com" target="_blank">Facebook</a>
      </div>
    </div>
  </footer>
</body>
</html>

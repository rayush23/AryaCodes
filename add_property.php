<?php
// add_property.php – Submit new property requests for admin approval
include 'header.php';
require_once 'db_connection.php';

// Only Agents (and Admins) can access this page
if (!isAgent()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // 1) Gather form inputs
    $name     = trim($_POST['property_name']);
    $type     = trim($_POST['property_type']);
    $location = trim($_POST['location']);
    $price    = floatval($_POST['price']);
    $size     = intval($_POST['size']);

    // 2) Handle image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir  = 'images/uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename   = uniqid('prop_', true) . ".$ext";
        $dest       = $targetDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $imagePath = $dest;
        } else {
            $error = 'Failed to upload image.';
        }
    }

    if (!$error) {
        // 3) Build payload array
        $payload = [
            'Name'     => $name,
            'Type'     => $type,
            'Location' => $location,
            'Price'    => $price,
            'Size'     => $size,
            'Image'    => $imagePath,
        ];
        $jsonPayload = json_encode($payload);

        // 4) Insert into property_requests (ADD action)
        $agentId = $meId;
        $sql     = "INSERT INTO property_requests (AgentID, PropertyID, Action, Payload)
                    VALUES (?, NULL, 'ADD', ?)";
        $stmt    = $conn->prepare($sql);
        $stmt->bind_param('is', $agentId, $jsonPayload);

        if ($stmt->execute()) {
            header("Location: agent_profile.php?success=Property+request+submitted");
            exit;
        } else {
            $error = 'Error submitting request: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request New Property – Braj Property</title>
  <link rel="stylesheet" href="add_property.css">
</head>
<body>
  <div class="add-container">
    <h2>Request New Property</h2>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="add_property.php" enctype="multipart/form-data">
      <label for="property_name">Name:</label>
      <input type="text" id="property_name" name="property_name" required>

      <label for="property_type">Type:</label>
      <input type="text" id="property_type" name="property_type" required>

      <label for="location">Location:</label>
      <input type="text" id="location" name="location" required>

      <label for="price">Price (MUR):</label>
      <input type="number" step="0.01" id="price" name="price" required>

      <label for="size">Size (sqft):</label>
      <input type="number" id="size" name="size" required>

      <label for="image">Image:</label>
      <input type="file" id="image" name="image" accept="image/*">

      <button type="submit">Submit for Approval</button>
    </form>
  </div>
</body>
</html>

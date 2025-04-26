<?php
// edit_property.php – Agent submits edit requests for admin approval
include 'header.php';
require_once 'db_connection.php';

// Only Agents (and Admins) can access this page
if (!isAgent()) {
    header('Location: login.php');
    exit;
}

// Get and validate property ID from query string
$propId = isset($_GET['PropertyID']) ? intval($_GET['PropertyID']) : 0;
if ($propId <= 0) {
    die('Invalid property ID.');
}

// Fetch existing property data, including city name and image path
$sql = "
    SELECT p.PropertyID, p.SalePrice, p.Description, p.Size,
           l.City AS Location, p.image AS ImagePath
      FROM property p
 LEFT JOIN location l ON p.LocationID = l.LocationID
     WHERE p.PropertyID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $propId);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows !== 1) {
    die('Property not found.');
}
$property = $result->fetch_assoc();
$stmt->close();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gather form inputs
    $newPrice    = floatval($_POST['price'] ?? 0);
    $newDesc     = trim($_POST['description'] ?? '');
    $newSize     = intval($_POST['size'] ?? 0);
    $newLocation = trim($_POST['location'] ?? '');

    // Handle image upload (optional)
    $imagePath = $property['ImagePath'];
    if (!empty($_FILES['image']['name'])) {
        $targetDir = 'images/uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prop_', true) . '.' . $ext;
        $dest     = $targetDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $imagePath = $dest;
        } else {
            $error = 'Failed to upload image.';
        }
    }

    if (!$error) {
        // Build payload array
        $payload = [
            'Price'       => $newPrice,
            'Description' => $newDesc,
            'Size'        => $newSize,
            'Location'    => $newLocation,
            'Image'       => $imagePath
        ];
        $jsonPayload = json_encode($payload);

        // Insert into property_requests table with EDIT action
        $agentId = $meId;
        $sql2    = "
            INSERT INTO property_requests
                (AgentID, PropertyID, Action, Payload)
            VALUES (?, ?, 'EDIT', ?)
        ";
        $ins = $conn->prepare($sql2);
        $ins->bind_param('iis', $agentId, $propId, $jsonPayload);
        if ($ins->execute()) {
            header('Location: agent_profile.php?success=Edit+request+submitted');
            exit;
        } else {
            $error = 'Error submitting edit request: ' . htmlspecialchars($conn->error);
        }
        $ins->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Property Edit – Braj Property</title>
  <link rel="stylesheet" href="add_property.css">
</head>
<body>
  <div class="add-container">
    <h2>Request Property Edit</h2>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="edit_property.php?PropertyID=<?= $propId ?>" enctype="multipart/form-data">
      <label for="price">Price (MUR):</label>
      <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($property['SalePrice']) ?>" required>

      <label for="description">Description:</label>
      <input type="text" id="description" name="description" value="<?= htmlspecialchars($property['Description']) ?>" required>

      <label for="size">Size (sqft):</label>
      <input type="number" id="size" name="size" value="<?= htmlspecialchars($property['Size']) ?>" required>

      <label for="location">Location:</label>
      <input type="text" id="location" name="location" value="<?= htmlspecialchars($property['Location']) ?>" required>

      <label for="image">Change Image (optional):</label>
      <input type="file" id="image" name="image" accept="image/*">

      <button type="submit">Submit Edit Request</button>
    </form>
  </div>
</body>
</html>

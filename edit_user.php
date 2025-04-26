<?php
// edit_property.php – Agent submits edit requests for admin approval
include 'header.php';
require_once 'db_connection.php';

// Only Agents (and Admins) can access this page
if (!isAgent()) {
    header('Location: login.php');
    exit;
}

// Get property ID from query string
$propId = isset($_GET['PropertyID']) ? intval($_GET['PropertyID']) : 0;

// Fetch existing property data
$stmt = $conn->prepare("SELECT PropertyID, SalePrice, Description, Size, LocationID FROM property WHERE PropertyID = ?");
$stmt->bind_param('i', $propId);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    die('Property not found.');
}
$property = $result->fetch_assoc();
$stmt->close();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gather form inputs
    $newPrice    = floatval($_POST['price']);
    $newDesc     = trim($_POST['description']);
    $newSize     = intval($_POST['size']);
    $newLocation = intval($_POST['location_id']); // assume a select of location IDs

    // Build payload array
    $payload = [
        'Price'       => $newPrice,
        'Description' => $newDesc,
        'Size'        => $newSize,
        'LocationID'  => $newLocation
    ];
    $jsonPayload = json_encode($payload);

    // Insert into property_requests table with EDIT action
    $agentId = $meId;
    $sql = "INSERT INTO property_requests (AgentID, PropertyID, Action, Payload) VALUES (?, ?, 'EDIT', ?)";
    $ins = $conn->prepare($sql);
    $ins->bind_param('iis', $agentId, $propId, $jsonPayload);
    if ($ins->execute()) {
        header('Location: agent_profile.php?success=Edit+request+submitted');
        exit;
    } else {
        $error = 'Error submitting edit request: ' . $conn->error;
    }
    $ins->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Property Edit – Braj Property</title>
  <link rel="stylesheet" href="property-add.css">
</head>
<body>
  <header>
    <h1>Request Property Edit</h1>
    <nav><a href="agent_profile.php">Back to Profile</a></nav>
  </header>
  <main>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
      <label for="price">Price:</label>
      <input type="number" step="0.01" id="price" name="price" value="<?= htmlspecialchars($property['SalePrice']) ?>" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" required><?= htmlspecialchars($property['Description']) ?></textarea>

      <label for="size">Size (sqft):</label>
      <input type="number" id="size" name="size" value="<?= htmlspecialchars($property['Size']) ?>" required>

      <label for="location_id">Location:</label>
      <select id="location_id" name="location_id">
        <?php
        // Fetch all locations for dropdown
        $locs = $conn->query("SELECT LocationID, City FROM location");
        while ($loc = $locs->fetch_assoc()): ?>
          <option value="<?= $loc['LocationID'] ?>" <?= $loc['LocationID']==$property['LocationID']?'selected':''?>>
            <?= htmlspecialchars($loc['City']) ?>
          </option>
        <?php endwhile; ?>
      </select>

      <button type="submit">Submit Edit Request</button>
    </form>
  </main>
</body>
</html>

<?php
// property-details.php – Display full property details and allow agents to request edits
include 'header.php';
require_once 'db_connection.php';

// Validate PropertyID from query string
if (empty($_GET['PropertyID']) || !is_numeric($_GET['PropertyID'])) {
    die('Invalid property ID.');
}
$propertyID = (int)$_GET['PropertyID'];

// Fetch property details including AgentID
$sql = "SELECT p.*, l.City, l.State, l.ZipCode, p.AgentID
        FROM property p
        LEFT JOIN location l ON p.LocationID = l.LocationID
        WHERE p.PropertyID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $propertyID);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows !== 1) {
    die('Property not found.');
}
$property = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details – Braj Property</title>
    <link rel="stylesheet" href="property-details.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($property['Description']) ?></h1>
        <nav>
            <a href="properties.php">Back to Listings</a>
            <?php if (isAgent() && $meId === (int)$property['AgentID']): ?>
                | <a href="edit_property.php?PropertyID=<?= $propertyID ?>">Request Edit</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="property-details-section">
            <div class="property-header">
                <h2><?= htmlspecialchars($property['Description']) ?></h2>
                <p class="property-location">
                    <?= htmlspecialchars($property['City']) ?>,
                    <?= htmlspecialchars($property['State']) ?>
                    <?= htmlspecialchars($property['ZipCode']) ?>
                </p>
            </div>

            <div class="property-container">
                <div class="property-image-wrapper">
                    <img
                        src="<?= htmlspecialchars($property['image'] ?: 'images/default-image.jpg') ?>"
                        alt="Property Image"
                        class="property-image"
                    >
                </div>
                <div class="property-info">
                    <p><strong>Price:</strong> MUR <?= number_format($property['SalePrice'], 2) ?></p>
                    <p><strong>Size:</strong> <?= (int)$property['Size'] ?> sqft</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Braj Property. All Rights Reserved.</p>
    </footer>
</body>
</html>
<?php $conn->close(); ?>

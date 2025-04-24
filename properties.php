<?php include 'header.php'; ?>

<?php
include 'db_connection.php';

// Initialize filter variables
$priceRange   = $_GET['price-range']   ?? '';
$location     = $_GET['location']      ?? '';
$propertyType = $_GET['property-type'] ?? '';

// Prepare the SQL query with filters (same logic as API)
$sql = "SELECT p.PropertyID, p.SalePrice, p.Description, p.Size, p.image, l.City AS LocationName
        FROM property p
        LEFT JOIN location l ON p.LocationID = l.LocationID
        WHERE 1=1";

if ($priceRange && strpos($priceRange, '-') !== false) {
    list($minPrice, $maxPrice) = explode('-', $priceRange);
    if ($minPrice) $sql .= " AND p.SalePrice >= " . floatval($minPrice);
    if ($maxPrice) $sql .= " AND p.SalePrice <= " . floatval($maxPrice);
} elseif ($priceRange) {
    $sql .= " AND p.SalePrice >= " . floatval($priceRange);
}

if ($location) {
    $locEsc = $conn->real_escape_string($location);
    $sql   .= " AND l.City = '{$locEsc}'";
}

if ($propertyType) {
    $typeEsc = $conn->real_escape_string($propertyType);
    if (in_array(strtolower($typeEsc), ['home','house'])) {
        $sql .= " AND (p.Description LIKE '%house%' OR p.Description LIKE '%home%')";
    } else {
        $sql .= " AND p.Description LIKE '%{$typeEsc}%'";
    }
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Properties - Braj Property</title>
  <link rel="stylesheet" href="properties.css">
">
</head>
<body>

  <header>
    <!-- your existing nav… -->
  </header>

  <section class="filters">
    <h2>Filter Properties</h2>
    <!-- A) Add id="filter-form" -->
    <form id="filter-form" action="properties.php" method="GET">
      <div class="filter-group">
        <label for="price-range">Price Range:</label>
        <select name="price-range" id="price-range">
          <option value="">Select Price Range</option>
          <option value="0-1000000"      <?= $priceRange==="0-1000000"      ? 'selected':'' ?>>MUR 0 – 1,000,000</option>
          <option value="1000001-2500000" <?= $priceRange==="1000001-2500000" ? 'selected':'' ?>>1,000,001 – 2,500,000</option>
          <option value="2500001-5000000" <?= $priceRange==="2500001-5000000" ? 'selected':'' ?>>2,500,001 – 5,000,000</option>
          <option value="5000001-20000000"<?= $priceRange==="5000001-20000000"? 'selected':'' ?>>5,000,001 – 20,000,000</option>
          <option value="20000001"        <?= $priceRange==="20000001"        ? 'selected':'' ?>>Above 20,000,000</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="location">Location:</label>
        <select name="location" id="location">
          <option value="">Select Location</option>
          <option value="Port Louis" <?= $location==="Port Louis" ? 'selected':'' ?>>Port Louis</option>
          <option value="Curepipe"   <?= $location==="Curepipe"   ? 'selected':'' ?>>Curepipe</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="property-type">Property Type:</label>
        <select name="property-type" id="property-type">
          <option value="">Select Property Type</option>
          <option value="villa"     <?= $propertyType==="villa"     ? 'selected':'' ?>>Villa</option>
          <option value="apartment" <?= $propertyType==="apartment" ? 'selected':'' ?>>Apartment</option>
          <option value="land"      <?= $propertyType==="land"      ? 'selected':'' ?>>Land</option>
          <option value="house"     <?= $propertyType==="house"     ? 'selected':'' ?>>House</option>
          <option value="home"      <?= $propertyType==="home"      ? 'selected':'' ?>>Home</option>
        </select>
      </div>

      <button type="submit" class="filter-button" id="filter-btn">Filter</button>
    </form>
  </section>

  <section class="properties-section">
    <h2>Available Properties</h2>
    <div class="property-list">
      <?php if ($result && $result->num_rows): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="property-card">
            <img
              src="<?= htmlspecialchars($row['image'] ?: 'images/default-image.jpg') ?>"
              alt="Property"
              class="property-image-details"
            >
            <h3><?= htmlspecialchars($row['Description']) ?></h3>
            <p class="price">MUR <?= number_format($row['SalePrice'], 2) ?></p>
            <p class="size"><?= (int)$row['Size'] ?> sqft</p>
            <p class="location"><?= htmlspecialchars($row['LocationName']) ?></p>
            <a href="property-details.php?PropertyID=<?= (int)$row['PropertyID'] ?>"
               class="view-btn">View Details</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No properties match your filters.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer>
    <!-- your existing footer… -->
  </footer>

  <!-- Include jQuery (if not already loaded) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- B) AJAX filter script -->
  <script>
    $(function() {
      $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        const params = $(this).serialize();
        $.getJSON('api/filter_properties.php?' + params, function(data) {
          let html = '';
          if (data.length) {
            data.forEach(function(p) {
              html += '<div class="property-card">'
                   +   '<img src="'+ p.image +'" alt="Property" class="property-image-details">'
                   +   '<h3>'+ p.Description +'</h3>'
                   +   '<p class="price">MUR '+ parseFloat(p.SalePrice).toFixed(2) +'</p>'
                   +   '<p class="size">'+ parseInt(p.Size,10) +' sqft</p>'
                   +   '<p class="location">'+ p.LocationName +'</p>'
                   +   '<a href="property-details.php?PropertyID='+ p.PropertyID +'" class="view-btn">View Details</a>'
                   + '</div>';
            });
          } else {
            html = '<p>No properties match your filters.</p>';
          }
          $('.property-list').html(html);
        });
      });
    });
  </script>
</body>
</html>

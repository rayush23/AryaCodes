<?php
include 'db_connection.php';
session_start();
include 'header.php';


// Initialize filter variables
$priceRange   = $_GET['price-range']   ?? '';
$location     = $_GET['location']      ?? '';
$propertyType = $_GET['property-type'] ?? '';

// Fetch dynamic locations for dropdown
$locations = [];
$locRes = $conn->query("SELECT DISTINCT City FROM location ORDER BY City ASC");
if ($locRes) {
    while ($l = $locRes->fetch_assoc()) {
        $locations[] = $l['City'];
    }
}

// Grab logged-in user ID for favorites
$userId = $_SESSION['user_id'] ?? 0;

// Build SQL with filters and favorite flag
$sql = "
  SELECT
    p.PropertyID,
    p.SalePrice,
    p.Description,
    p.Size,
    p.image,
    IF(f.UserID IS NULL, 0, 1) AS is_fav
  FROM property p
  LEFT JOIN location l ON p.LocationID = l.LocationID
  LEFT JOIN favorite f
    ON f.PropertyID = p.PropertyID
   AND f.UserID     = {$userId}
  WHERE 1=1
";

// Apply filters
if ($priceRange) {
    if (strpos($priceRange, '-') !== false) {
        list($minPrice, $maxPrice) = explode('-', $priceRange);
        if ($minPrice !== '') {
            $sql .= " AND p.SalePrice >= " . floatval($minPrice);
        }
        if ($maxPrice !== '') {
            $sql .= " AND p.SalePrice <= " . floatval($maxPrice);
        }
    } else {
        $sql .= " AND p.SalePrice >= " . floatval($priceRange);
    }
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
</head>
<body>

  <!-- Site Header & Navigation -->
  <header>
    <div class="site-container">
      <a href="index.php" class="logo">Braj Property</a>
      <nav>
        <ul class="nav-list">
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="properties.php" class="active">Properties</a></li>
          <li><a href="agents.php">Agents</a></li>
          <li><a href="contact.php">Contact</a></li>
          <?php if (isLoggedIn() && isClient()): ?>
            <li><a href="client_appointments.php">My Appointments</a></li>
          <?php endif; ?>
          <?php if (isLoggedIn() && isAgent()): ?>
            <li><a href="agent_appointments.php">Agent Panel</a></li>
          <?php endif; ?>
          <?php if (isAdmin()): ?>
            <li><a href="admin_dashboard.php">Admin Panel</a></li>
          <?php endif; ?>
          <?php if (isLoggedIn()): ?>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Filters -->
  <section class="filters site-container">
    <h2>Filter Properties</h2>
    <form id="filter-form" action="properties.php" method="GET" class="filter-form">
      <div class="filter-group">
        <label for="price-range">Price Range:</label>
        <select name="price-range" id="price-range">
          <option value="" <?= $priceRange===''?'selected':'' ?>>Any</option>
          <option value="0-1000000" <?= $priceRange==='0-1000000'?'selected':'' ?>>0–1,000,000</option>
          <option value="1000001-2500000" <?= $priceRange==='1000001-2500000'?'selected':'' ?>>1,000,001–2,500,000</option>
          <option value="2500001-5000000" <?= $priceRange==='2500001-5000000'?'selected':'' ?>>2,500,001–5,000,000</option>
          <option value="5000001-20000000" <?= $priceRange==='5000001-20000000'?'selected':'' ?>>5,000,001–20,000,000</option>
          <option value="20000001" <?= $priceRange==='20000001'?'selected':'' ?>>Above 20,000,000</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="location">Location:</label>
        <select name="location" id="location">
          <option value="" <?= $location===''?'selected':'' ?>>Any</option>
          <?php foreach ($locations as $city): ?>
            <option value="<?= htmlspecialchars($city) ?>" <?= $location===$city?'selected':'' ?>>
              <?= htmlspecialchars($city) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-group">
        <label for="property-type">Property Type:</label>
        <select name="property-type" id="property-type">
          <option value="" <?= $propertyType===''?'selected':'' ?>>Any</option>
          <option value="villa"     <?= $propertyType==='villa'?'selected':'' ?>>Villa</option>
          <option value="apartment" <?= $propertyType==='apartment'?'selected':'' ?>>Apartment</option>
          <option value="land"      <?= $propertyType==='land'?'selected':'' ?>>Land</option>
          <option value="house"     <?= $propertyType==='house'?'selected':'' ?>>House</option>
          <option value="home"      <?= $propertyType==='home'?'selected':'' ?>>Home</option>
        </select>
      </div>
      <button type="submit" class="filter-button">Filter</button>
    </form>
  </section>

  <!-- Property Cards -->
  <section class="properties-section site-container">
    <h2>Available Properties</h2>
    <div class="property-list">
      <?php if ($result && $result->num_rows): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="property-card" data-id="<?= $row['PropertyID'] ?>">
            <img src="<?= htmlspecialchars($row['image'] ?: 'images/default-image.jpg') ?>" alt="Property Image">
            <h3><?= htmlspecialchars($row['Description']) ?></h3>
            <p class="price">MUR <?= number_format($row['SalePrice'], 2) ?></p>
            <p class="size"><?= htmlspecialchars($row['Size']) ?> sqft</p>

            <!-- Favourite toggle -->
            <button class="btn-fav">
              <span class="star <?= $row['is_fav'] ? 'on' : 'off' ?>">★</span>
            </button>

            <a href="property-details.php?PropertyID=<?= $row['PropertyID'] ?>" class="view-btn">View Details</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No properties match your filters.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Site Footer -->
  <footer>
    <div class="site-container">
      <p>&copy; <?= date('Y') ?> Braj Property. All Rights Reserved.</p>
      <div class="social-buttons">
        <a href="https://wa.me/" target="_blank">WhatsApp</a>
        <a href="https://facebook.com" target="_blank">Facebook</a>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(function(){
    $('.btn-fav').on('click', function(){
      var $btn  = $(this),
          $card = $btn.closest('.property-card'),
          propID = $card.data('id'),
          isOn   = $btn.find('.star').hasClass('on'),
          action = isOn ? 'remove' : 'add';

      $.ajax({
        url: 'mark_favorite.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ propertyID: propID, action: action }),
        dataType: 'json'
      })
      .done(function(resp){
        if (resp.success) {
          $btn.find('.star').toggleClass('on off');
        } else {
          alert('Error toggling favorite: ' + resp.error);
        }
      })
      .fail(function(){
        alert('Network error.');
      });
    });
  });
  </script>

</body>
</html>

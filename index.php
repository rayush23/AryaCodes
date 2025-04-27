<?php include 'header.php'; ?>
<?php
include 'db_connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Filter variables
$priceRange   = isset($_GET['price-range']) ? $_GET['price-range'] : '';
$location     = isset($_GET['location'])    ? $_GET['location']    : '';
$propertyType = isset($_GET['property-type'])? $_GET['property-type']: '';

// Fetch dynamic home content
$homeContent = '';
$sqlHome = "
  SELECT content
    FROM site_content
   WHERE page='home'
     AND content_key='main'
   LIMIT 1
";
$resHome = mysqli_query($conn, $sqlHome);
if ($resHome && mysqli_num_rows($resHome) > 0) {
    $homeContent = mysqli_fetch_assoc($resHome)['content'];
}

// Build properties query
$userId = $_SESSION['user_id'] ?? 0;
$sql = "
  SELECT p.PropertyID, p.SalePrice, p.Description, p.Size, p.image,
         IF(f.UserID IS NULL,0,1) AS is_fav
    FROM property p
    LEFT JOIN location l ON p.LocationID = l.LocationID
    LEFT JOIN favorite f ON f.PropertyID=p.PropertyID AND f.UserID={$userId}
   WHERE 1=1
";
if ($priceRange) {
    if (strpos($priceRange, '-') !== false) {
        list($min, $max) = explode('-', $priceRange);
        if ($min !== '') $sql .= " AND p.SalePrice>=".floatval($min);
        if ($max !== '') $sql .= " AND p.SalePrice<=".floatval($max);
    } else {
        $sql .= " AND p.SalePrice>=".floatval($priceRange);
    }
}
if ($location) {
    $locEsc = mysqli_real_escape_string($conn, $location);
    $sql   .= " AND l.City LIKE '%{$locEsc}%'";
}
if ($propertyType) {
    $typeEsc = mysqli_real_escape_string($conn, $propertyType);
    if (in_array(strtolower($typeEsc), ['home','house'])) {
        $sql .= " AND (p.Description LIKE '%house%' OR p.Description LIKE '%home%')";
    } else {
        $sql .= " AND p.Description LIKE '%{$typeEsc}%'";
    }
}
$sql .= " LIMIT 4";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Braj Property - Home</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <!-- Main Navigation -->
  <header>
  <div class="logo">
            <h1>Braj Property</h1>
        </div>
    <nav>
      <ul class="nav-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="properties.php">Properties</a></li>
        <li><a href="agents.php">Agents</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if (isLoggedIn() && isClient()): ?>
          <li><a href="favorites.php">My Favorites</a></li>
          <li><a href="client_appointments.php">My Appointments</a></li>
        <?php endif; ?>
        <?php if (isLoggedIn() && isAgent()): ?>
          <li><a href="agent_appointments.php">Agent Panel</a></li>
        <?php endif; ?>
        <?php if (isLoggedIn() && isAdmin()): ?>
          <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
        <?php if (isLoggedIn()): ?>
          <li><a href="userAccount.php">Your Account</a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="signup.php">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  

  <!-- Hero Section -->
  <section class="hero" style="background:url('images/tokyo.jpg')no-repeat center/cover;height:400px;">
    <div class="site-container">
      <h2>Find Your Dream Property</h2>
      <p>Explore the best listings across Mauritius.</p>
      <form class="search-form" method="GET" action="properties.php">
        <input type="text" name="location" placeholder="Search by location, type...">
        <button type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- Filter Section -->
  <section class="filters site-container">
    <h2>Filter Properties</h2>
    <form action="properties.php" method="GET" class="filter-form">
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
          <option value="Port Louis" <?= $location==='Port Louis'?'selected':'' ?>>Port Louis</option>
          <option value="Curepipe" <?= $location==='Curepipe'?'selected':'' ?>>Curepipe</option>
        </select>
      </div>
      <div class="filter-group">
        <label for="property-type">Property Type:</label>
        <select name="property-type" id="property-type">
          <option value="" <?= $propertyType===''?'selected':'' ?>>Any</option>
          <option value="villa" <?= $propertyType==='villa'?'selected':'' ?>>Villa</option>
          <option value="apartment" <?= $propertyType==='apartment'?'selected':'' ?>>Apartment</option>
          <option value="land" <?= $propertyType==='land'?'selected':'' ?>>Land</option>
          <option value="house" <?= $propertyType==='house'?'selected':'' ?>>House</option>
          <option value="home" <?= $propertyType==='home'?'selected':'' ?>>Home</option>
        </select>
      </div>
      <button type="submit" class="filter-button">Filter</button>
    </form>
  </section>

  <!-- Dynamic CMS Content -->
  <section class="dynamic-home-content site-container">
    <div id="home-content"><?= $homeContent ?></div>
    <pre id="raw-home-json" style="display:none; background:#eef; padding:1rem; border-radius:4px; overflow:auto;"></pre>
  </section>

  <!-- Featured Properties -->
  <section class="featured-properties site-container">
    <h2>Featured Properties</h2>
    <div class="property-list">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="property-card" data-id="<?= $row['PropertyID'] ?>">
            <img src="<?= htmlspecialchars($row['image']?:'images/default.jpg') ?>" alt="Property Image">
            <h3><?= htmlspecialchars($row['Description']) ?></h3>
            <p class="price">MUR <?= number_format($row['SalePrice'],2) ?></p>
            <a href="property-details.php?PropertyID=<?= $row['PropertyID'] ?>" class="view-btn">View Details</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No properties available matching your filters.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="site-container">
    <p>&copy; <?= date('Y') ?> Braj Property. All Rights Reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(function(){
      $.getJSON('api/content.php?page=home&key=main')
        .done(function(data){
          $('#home-content').html(data.content);
          $('#raw-home-json').show().text(JSON.stringify(data,null,2));
        })
        .fail(function(err){ console.error('Failed to load home content:',err); });
    });
  </script>

</body>
</html>

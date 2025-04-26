<?php include 'header.php'; ?>
<?php
include 'db_connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//  filter variables 
$priceRange = isset($_GET['price-range']) ? $_GET['price-range'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$propertyType = isset($_GET['property-type']) ? $_GET['property-type'] : '';

//  SQL query with filters
$sql = "SELECT p.PropertyID, p.SalePrice, p.Description, p.Size, p.image 
        FROM property p 
        LEFT JOIN location l ON p.LocationID = l.LocationID 
        WHERE 1=1";


if ($priceRange) {
    //  dash
    if (strpos($priceRange, '-') !== false) {
        list($minPrice, $maxPrice) = explode('-', $priceRange);

        if ($minPrice) {
            $sql .= " AND p.SalePrice >= $minPrice"; // minimum price
        }

        if ($maxPrice) {
            $sql .= " AND p.SalePrice <= $maxPrice"; // max price 
        }
    } else {
        // If no dash, minimum price filter
        $sql .= " AND p.SalePrice >= " . (int)$priceRange;
    }
}

// Filter based on location
if ($location) {
    $sql .= " AND l.City LIKE '%" . mysqli_real_escape_string($conn, $location) . "%'";
}

// property type in the description
if ($propertyType) {
    if (strtolower($propertyType) == 'home') {
        $sql .= " AND (p.Description LIKE '%house%' OR p.Description LIKE '%home%')";
    } elseif (strtolower($propertyType) == 'house') {
        $sql .= " AND (p.Description LIKE '%house%' OR p.Description LIKE '%home%')";
    } else {
        $sql .= " AND p.Description LIKE '%" . mysqli_real_escape_string($conn, $propertyType) . "%'";
    }
}
$sql .= " LIMIT 4"; // Limit to 4 results
$result = $conn->query($sql);

$result = $conn->query($sql);

$homeContent = '';
$sql = "
  SELECT content 
    FROM site_content 
   WHERE page='home' 
     AND content_key='main' 
   LIMIT 1
";
$res = mysqli_query($conn, $sql);
if ($res && mysqli_num_rows($res) > 0) {
    $homeContent = mysqli_fetch_assoc($res)['content'];
}
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
    <header>
        <div class="logo">
            <h1>Braj Property</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="properties.php">Properties</a></li>
                <li><a href="agents.php">Agents</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isLoggedIn() && isClient()): ?>
                  <li><a href="client_appointments.php">My Appointments</a></li>
                <?php endif; ?>

                <!-- Check if the user is logged in and show account options -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="userAccount.php">Your Account</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="hero" style="background: url('images/tokyo.jpg') no-repeat center center/cover; height: 400px;">
        <h2>Find Your Dream Property</h2>
        <p>Explore the best listings across Mauritius.</p>
        <form class="search-form" method="GET" action="properties.php">
            <input type="text" name="location" placeholder="Search by location, type...">
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="filters">
        <h2>Filter Properties</h2>
        <form action="properties.php" method="GET" class="filter-form">
            <div class="filter-group">
                <label for="price-range">Price Range:</label>
                <select name="price-range" id="price-range">
                    <option value="">Select Price Range</option>
                    <option value="0-1000000" <?php echo ($priceRange == "0-1000000") ? 'selected' : ''; ?>>MUR 0 - 1000,000</option>
                    <option value="1000001-2500000" <?php echo ($priceRange == "1000001-2500000") ? 'selected' : ''; ?>>MUR 1000,001 - 2,500,000</option>
                    <option value="2500001-5000000" <?php echo ($priceRange == "2500001-5000000") ? 'selected' : ''; ?>>MUR 2,500,001 - 5,000,000</option>
                    <option value="5000001-20000000" <?php echo ($priceRange == "5000001-20000000") ? 'selected' : ''; ?>>MUR 5,000,001 - 20,000,000</option>
                    <option value="20000001" <?php echo ($priceRange == "20000001") ? 'selected' : ''; ?>>Above MUR 20,000,000</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="location">Location:</label>
                <select name="location" id="location">
                    <option value="">Select Location</option>
                    <option value="Port Louis" <?php echo ($location == "Port Louis") ? 'selected' : ''; ?>>Port Louis</option>
                    <option value="Curepipe" <?php echo ($location == "Curepipe") ? 'selected' : ''; ?>>Curepipe</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="property-type">Property Type:</label>
                <select name="property-type" id="property-type">
                    <option value="">Select Property Type</option>
                    <option value="villa" <?php echo ($propertyType == "villa") ? 'selected' : ''; ?>>Villa</option>
                    <option value="apartment" <?php echo ($propertyType == "apartment") ? 'selected' : ''; ?>>Apartment</option>
                    <option value="land" <?php echo ($propertyType == "land") ? 'selected' : ''; ?>>Land</option>
                    <option value="house" <?php echo ($propertyType == "house") ? 'selected' : ''; ?>>House</option>
                    <option value="home" <?php echo ($propertyType == "home") ? 'selected' : ''; ?>>Home</option>
                </select>
            </div>

            <button type="submit" class="filter-button">Filter</button>
        </form>
    </section>
    <section class="dynamic-home-content">
  <div id="home-content">
    <?= $homeContent ?>
  </div>
  <pre id="raw-home-json" style="display:none;background:#eef;padding:1rem;border-radius:4px;overflow:auto;"></pre>
</section>


    <section class="featured-properties">
        <h2>Featured Properties</h2>
        <div class="property-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='property-card'>";


                    $image = isset($row['image']) ? $row['image'] : 'images/default-image.jpg';
                    $description = isset($row['Description']) ? $row['Description'] : 'No description available';
                    $price = isset($row['SalePrice']) ? number_format($row['SalePrice'], 2) : '0.00';
                    $propertyID = isset($row['PropertyID']) ? $row['PropertyID'] : '#';

                    echo "<img src='" . $image . "' alt='Property Image'>";
                    echo "<h3>" . htmlspecialchars($description) . "</h3>";
                    echo "<p class='price'>MUR " . $price . "</p>";

                    echo "<a href='property-details.php?PropertyID=" . $propertyID . "' class='view-btn'>View Details</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No properties available matching your filters.</p>";
            }
            ?>
        </div>
    </section>

    <footer style="background-color: #222; color: #fff; padding: 20px; text-align: center;">
        <p>&copy; 2024 Braj Property. All Rights Reserved.</p>
        <div class="social-buttons" style="margin-top: 10px;">
            <a href="https://wa.me/" class="whatsapp" target="_blank" style="color: #25d366; text-decoration: none; margin-right: 10px;">WhatsApp</a>
            <a href="https://facebook.com" class="facebook" target="_blank" style="color: #3b5998; text-decoration: none;">Facebook</a>
        </div>
    </footer>

    <?php
    // Close connection after use
    $conn->close();
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(function(){
      $.getJSON('api/content.php?page=home&key=main')
        .done(function(data){
          // render fetched HTML
          $('#home-content').html(data.content);
          // show raw JSON
          $('#raw-home-json')
            .show()
            .text(JSON.stringify(data, null, 2));
        })
        .fail(function(err){
          console.error('Failed to load home content:', err);
        });
    });
  </script>


</body>

</html>
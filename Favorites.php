<?php
// favorites.php – Client-only page to list their favorited properties

include 'db_connection.php';
session_start();
include 'header.php';  // includes isLoggedIn(), isClient(), nav

// 1) Restrict to logged-in clients
if (!isLoggedIn() || !isClient()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// 2) Fetch all favorited properties for this user
$sql = "
  SELECT
    p.PropertyID,
    p.SalePrice,
    p.Description,
    p.Size,
    p.image,
    l.City
  FROM favorite f
  JOIN property p
    ON f.PropertyID = p.PropertyID
  LEFT JOIN location l
    ON p.LocationID = l.LocationID
  WHERE f.UserID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Favorites – Braj Property</title>
  <link rel="stylesheet" href="properties.css">
</head>
<body>

  <main class="site-container">
    <h1>My Favorite Properties</h1>
    <div class="property-list">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="property-card" data-id="<?= $row['PropertyID'] ?>">
            <img src="<?= htmlspecialchars($row['image'] ?: 'images/default-image.jpg') ?>" alt="Property Image">
            <h3><?= htmlspecialchars($row['Description']) ?></h3>
            <p class="location"><?= htmlspecialchars($row['City']) ?></p>
            <p class="price">MUR <?= number_format($row['SalePrice'], 2) ?></p>
            <p class="size"><?= htmlspecialchars($row['Size']) ?> sqft</p>

            <!-- Unfavorite toggle -->
            <button class="btn-fav">
              <span class="star on">★</span>
            </button>

            <a href="property-details.php?PropertyID=<?= $row['PropertyID'] ?>" class="view-btn">View Details</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>You have no favorite properties yet.</p>
      <?php endif; ?>
    </div>
  </main>

  <!-- Unfavorite AJAX script -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(function(){
    $('.btn-fav').on('click', function(){
      var $btn   = $(this),
          $card  = $btn.closest('.property-card'),
          propID = $card.data('id');

      $.ajax({
        url: 'mark_favorite.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ propertyID: propID, action: 'remove' }),
        dataType: 'json'
      })
      .done(function(resp){
        if (resp.success) {
          // Remove the card from the list
          $card.fadeOut(300, function(){ $(this).remove(); });
        } else {
          alert('Error removing favorite: ' + resp.error);
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

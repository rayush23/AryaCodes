<?php
// Favorites.php – combined JSON endpoint + “My Favorites” page

// 1) Always start the session and open your DB connection first
session_start();
require_once 'db_connection.php';  // defines $conn

// 2) If it’s an AJAX POST, handle JSON and exit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=UTF-8');

    // Decode JSON payload
    $input  = json_decode(file_get_contents('php://input'), true);
    $propID = isset($input['propertyID']) ? (int)$input['propertyID'] : 0;
    $action = $input['action'] ?? '';

    // Verify logged-in user
    $userId = $_SESSION['user_id'] ?? 0;
    if (! $userId || ! in_array($action, ['add','remove'])) {
        echo json_encode([
          'success' => false,
          'error'   => 'Invalid user or action.'
        ]);
        exit;
    }

    // Perform DB action
    if ($action === 'remove') {
        $stmt = $conn->prepare(
          "DELETE FROM favorite WHERE UserID = ? AND PropertyID = ?"
        );
    } else { // add
        $stmt = $conn->prepare(
          "INSERT IGNORE INTO favorite (UserID, PropertyID) VALUES (?, ?)"
        );
    }
    $stmt->bind_param('ii', $userId, $propID);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
          'success' => false,
          'error'   => $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// 3) From here down is your GET-only page

// Redirect non-clients before any HTML
require_once 'header.php';     // this defines isLoggedIn(), isClient(), nav, etc.
if (! isLoggedIn() || ! isClient()) {
    header('Location: login.php');
    exit;
}

// Fetch this client’s favorites
$userId = $_SESSION['user_id'];
$sql = "
  SELECT p.PropertyID, p.SalePrice, p.Description,
         p.Size, p.image, l.City
    FROM favorite f
    JOIN property p ON f.PropertyID = p.PropertyID
    LEFT JOIN location l ON p.LocationID = l.LocationID
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
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Favorites – Braj Property</title>
  <link rel="stylesheet" href="properties.css">
</head>
<body>
  <main class="site-container">
    <h1>My Favorite Properties</h1>
    <div class="property-list">
      <?php if ($result->num_rows): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="property-card" data-id="<?= $row['PropertyID'] ?>">
            <img src="<?= htmlspecialchars($row['image'] ?: 'images/default.jpg') ?>"
                 alt="Property">
            <h3><?= htmlspecialchars($row['Description']) ?></h3>
            <p class="location"><?= htmlspecialchars($row['City']) ?></p>
            <p class="price">MUR <?= number_format($row['SalePrice'],2) ?></p>
            <p class="size"><?= htmlspecialchars($row['Size']) ?> sqft</p>
            <button class="btn-fav">
              <span class="star on">★</span>
            </button>
            <a href="property-details.php?PropertyID=<?= $row['PropertyID'] ?>"
               class="view-btn">View Details</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>You have no favorite properties yet.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(function(){
    $('.btn-fav').on('click', function(){
      var $btn  = $(this),
          card = $btn.closest('.property-card'),
          id   = card.data('id');

      $.ajax({
        url: 'Favorites.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ propertyID: id, action: 'remove' }),
        dataType: 'json'
      })
      .done(function(resp){
        if (resp.success) {
          card.fadeOut(300, function(){ $(this).remove(); });
        } else {
          alert('Error: ' + resp.error);
        }
      })
      .fail(function(_, status){
        alert('Network error (' + status + ')');
      });
    });
  });
  </script>
</body>
</html>

<?php
// about.php – dynamically loads About Us content via AJAX
include 'db_connection.php';
session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch server-rendered fallback
$aboutContent = '';
$sqlAbout = "
  SELECT content
    FROM site_content
   WHERE page='about'
     AND content_key='main'
   LIMIT 1
";
$resAbout = mysqli_query($conn, $sqlAbout);
if ($resAbout && mysqli_num_rows($resAbout) > 0) {
    $aboutContent = mysqli_fetch_assoc($resAbout)['content'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>About Us — Braj Property</title>
  <link rel="stylesheet" href="about.css" type="text/css">
</head>
<body>
  <!-- ==== SITE HEADER & NAVIGATION ==== -->
  <header>
    <div class="site-container">
      <a href="index.php" class="logo">Braj Property</a>
      <nav>
        <ul class="nav-list">
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php" class="active">About</a></li>
          <li><a href="properties.php">Properties</a></li>
          <li><a href="contact.php">Contact</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="signup.php">Sign Up</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- ==== ABOUT CONTENT (CMS-DRIVEN) ==== -->
  <main class="about-section site-container">
    <!-- Rendered fallback/content container -->
    <div id="about-content">
      <?= $aboutContent ?>
    </div>
    <!-- Optional: raw JSON for debugging -->
    <pre id="raw-about-json" style="display:none;background:#eef;padding:1rem;border-radius:4px;overflow:auto;"></pre>
  </main>

  <!-- ==== SITE FOOTER ==== -->
  <footer>
    <div class="site-container">
      <p>&copy; <?= date('Y') ?> Braj Property. All Rights Reserved.</p>
      <div class="social-buttons">
        <a href="https://wa.me/" target="_blank">WhatsApp</a>
        <a href="https://facebook.com" target="_blank">Facebook</a>
      </div>
    </div>
  </footer>

  <!-- jQuery & AJAX loader for About content -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(function(){
      $.getJSON('api/content.php?page=about&key=main')
        .done(function(data){
          $('#about-content').html(data.content);
          $('#raw-about-json')
            .show()
            .text(JSON.stringify(data, null, 2));
        })
        .fail(function(err){
          console.error('Failed to load about content:', err);
        });
    });
  </script>
</body>
</html>

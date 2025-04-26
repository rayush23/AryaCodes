<?php
// content_management.php
include 'header.php';
require_once 'db_connection.php';
if (!isAdmin()) {
  header('Location: login.php'); exit;
}
$pageTitle = 'Content Management';

// Load blocks
$blocks = [];
$res = $conn->query("SELECT page,content_key AS ckey,content FROM site_content");
while ($r=$res->fetch_assoc()) {
  $blocks["{$r['page']}__{$r['ckey']}"] = $r['content'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?=htmlspecialchars($pageTitle)?></title>
  <link rel="stylesheet" href="admin_dashboard.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <header>
    <h1><?=htmlspecialchars($pageTitle)?></h1>
    <a href="admin_dashboard.php" class="btn">← Dashboard</a>
    <a href="logout.php" class="btn cancel-btn">Logout</a>
  </header>
  <main style="padding:20px; max-width:800px; margin:auto;">
    <form id="home-form">
      <h2>Home Page</h2>
      <textarea id="home-content" rows="6" style="width:100%;"><?=htmlspecialchars($blocks['home__main']??'')?></textarea>
      <button id="save-home" class="btn">Save Home</button>
    </form>

    <hr style="margin:40px 0;">

    <form id="about-form">
      <h2>About Us</h2>
      <textarea id="about-content" rows="6" style="width:100%;"><?=htmlspecialchars($blocks['about__main']??'')?></textarea>
      <button id="save-about" class="btn">Save About</button>
    </form>
  </main>

  <script>
  $(function(){
    function saveBlock(page, key, textareaSelector) {
      const content = $(textareaSelector).val();
      $.ajax({
        url: 'api/content.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ page, key, content }),
        dataType: 'json'
      }).done(function(resp){
        alert(`Saved ${page}/${key}!`);
      }).fail(function(xhr){
        const err = xhr.responseJSON?.error || 'Unknown error';
        alert(`Save failed: ${err}`);
      });
    }

    $('#save-home').click(function(e){
      e.preventDefault();
      saveBlock('home','main','#home-content');
    });

    $('#save-about').click(function(e){
      e.preventDefault();
      saveBlock('about','main','#about-content');
    });
  });
  </script>
</body>
</html>

<?php
// admin_dashboard.php – Main Admin landing page
include 'header.php';            // purely PHP session+helpers
require_once 'db_connection.php';

// Only Admins can access
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}
$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
  <header style="position: relative;">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <a href="logout.php"
       style="
         position: absolute;
         top: 20px;
         right: 20px;
         background: #e74c3c;
         color: #fff;
         padding: 6px 12px;
         border-radius: 4px;
         text-decoration: none;
         transition: background 0.3s;
       "
       onmouseover="this.style.background='#c0392b';"
       onmouseout="this.style.background='#e74c3c';"
    >Logout</a>
  </header>


  <main>
    <section class="dashboard-overview">
      <h2>Welcome, <?= htmlspecialchars($meName) ?></h2>
      <p>Use the cards below to manage Braj Property’s platform.</p>
    </section>

    <section class="dashboard-cards">
      <div class="card">
        <h3>Property Requests</h3>
        <p>Review pending property additions, edits, and deletions.</p>
        <a href="pending_requests.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Appointments</h3>
        <p>Manage client-agent appointments.</p>
        <a href="admin_appointments.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Site Analytics</h3>
        <p>View traffic and engagement metrics.</p>
        <a href="site_analytics.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Content Management</h3>
        <p>Edit Home/About site copy</p>
        <a href="content_management.php" class="btn">Go</a>
      </div>

    </section>
  </main>
</body>
</html>

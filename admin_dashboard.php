<?php
// admin_dashboard.php – Main Admin landing page
include 'header.php';
require_once 'db_connection.php';

// Only Admins can access
if (!isAdmin()) {
    // Non-admins go to login
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard – Braj Property</title>
  <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
  <header>
    <h1>Admin Dashboard</h1>
    <nav>
      <ul class="admin-nav">
        <li><a href="pending_requests.php">Property Requests</a></li>
        <li><a href="appointments.php">Appointment Queue</a></li>
        <li><a href="user_management.php">User Management</a></li>
        <li><a href="content_management.php">Content Management</a></li>
        <li><a href="transactions.php">Transactions &amp; Commissions</a></li>
        <li><a href="site_analytics.php">Site Analytics</a></li>
        <li><a href="system_logs.php">System Settings &amp; Logs</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="dashboard-overview">
      <h2>Welcome, <?= htmlspecialchars($meName) ?></h2>
      <p>Use the menu above to manage Braj Property’s platform.</p>
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
        <a href="appointments.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>User Management</h3>
        <p>Create, update, or delete user accounts.</p>
        <a href="user_management.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Content Management</h3>
        <p>Edit site content and featured properties.</p>
        <a href="content_management.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Transactions</h3>
        <p>View transactions and manage commissions.</p>
        <a href="transactions.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>Site Analytics</h3>
        <p>View traffic and engagement metrics.</p>
        <a href="site_analytics.php" class="btn">Go</a>
      </div>
      <div class="card">
        <h3>System Logs</h3>
        <p>Review admin actions and system settings.</p>
        <a href="system_logs.php" class="btn">Go</a>
      </div>
    </section>
  </main>
</body>
</html>


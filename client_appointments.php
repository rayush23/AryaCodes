<?php
// client_appointments.php

// 1) Bootstrap session + role helpers
include 'header.php';
require_once 'db_connection.php';

// 2) Guard: only logged-in Clients can view this page
if (!isLoggedIn() || !isClient()) {
    header('Location: login.php');
    exit;
}

// 3) Page metadata
$pageTitle = 'My Appointments';
$clientId  = $_SESSION['user_id'];

// 4) Fetch this client’s upcoming appointments
$stmt = $conn->prepare("
    SELECT a.AppointmentID, a.Date, a.Time,
           p.Description AS Property,
           u.Name        AS AgentName
      FROM appointment a
      JOIN property   p ON a.PropertyID = p.PropertyID
      JOIN user       u ON a.AgentID    = u.UserID
     WHERE a.ClientID = ?
     ORDER BY a.Date, a.Time
");
$stmt->bind_param('i', $clientId);
$stmt->execute();
$appts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="client_appointments.css">

</head>
<body>

  <header>
    <div class="logo">
      <a href="index.php">Braj Property</a>
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="properties.php">Properties</a></li>
        <li><a href="agent.php">Agents</a></li>
        <li><a href="contact.php">Contact</a></li>

        <?php if (isLoggedIn() && isClient()): ?>
          <li><a href="client_appointments.php">My Appointments</a></li>
        <?php endif; ?>

        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main style="padding: 20px;">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>

    <?php if (count($appts) > 0): ?>
      <table id="client-appts">
        <thead>
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Property</th>
            <th>Agent</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($appts as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['Date']) ?></td>
              <td><?= htmlspecialchars(substr($a['Time'],0,5)) ?></td>
              <td><?= htmlspecialchars($a['Property']) ?></td>
              <td><?= htmlspecialchars($a['AgentName']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="message">You have no upcoming appointments.</p>
    <?php endif; ?>
  </main>

</body>
</html>

<?php
include 'header.php';
require_once 'db_connection.php';

// Only Agents (and Admins) can access
if (!isAgent()) {
    header('Location: login.php');
    exit;
}

$agentId = $meId;

// Fetch this agent's properties
$propStmt = $conn->prepare(
    "SELECT PropertyID, Description, SalePrice, Size, image
     FROM property
     WHERE AgentID = ?"
);
$propStmt->bind_param('i', $agentId);
$propStmt->execute();
$propResult = $propStmt->get_result();
$propStmt->close();

// Fetch the current agent's property requests
$reqStmt = $conn->prepare(
    "SELECT RequestID, Action, Status, CreatedAt
     FROM property_requests
     WHERE AgentID = ?
     ORDER BY CreatedAt DESC"
);
$reqStmt->bind_param('i', $agentId);
$reqStmt->execute();
$reqResult = $reqStmt->get_result();
$reqStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Profile – Braj Property</title>
    <link rel="stylesheet" href="agent_profile.css">
</head>
<body>
    <header>
        <h1>Agent Panel</h1>
        <nav>
            <ul>
                <li>Welcome, <?= htmlspecialchars($meName) ?></li>
                <li><a href="add_property.php" class="active">Request New Property</a></li>
                <li><a href="agent_appointments.php">Appointments</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Properties -->
        <section>
            <h2>Your Properties</h2>
            <?php if ($propResult && $propResult->num_rows): ?>
                <div class="property-list">
                    <?php while ($p = $propResult->fetch_assoc()): ?>
                        <div class="property-card">
                            <img src="<?= htmlspecialchars($p['image'] ?: 'images/default-image.jpg') ?>"
                                 alt="Property"
                                 class="property-thumb">
                            <h3><?= htmlspecialchars($p['Description']) ?></h3>
                            <p>Price: MUR <?= number_format($p['SalePrice'], 2) ?></p>
                            <p>Size: <?= (int)$p['Size'] ?> sqft</p>
                            <div class="property-actions">
                                <a href="edit_property.php?PropertyID=<?= (int)$p['PropertyID'] ?>" class="btn">Request Edit</a>
                                <a href="delete_request.php?PropertyID=<?= (int)$p['PropertyID'] ?>" class="btn btn-danger">Request Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>You have no listed properties yet.</p>
            <?php endif; ?>
        </section>

        <!-- Requests -->
        <section>
            <h2>Your Property Requests</h2>
            <?php if ($reqResult && $reqResult->num_rows): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $reqResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $r['RequestID'] ?></td>
                                <td><?= htmlspecialchars($r['Action']) ?></td>
                                <td><?= htmlspecialchars($r['Status']) ?></td>
                                <td><?= htmlspecialchars($r['CreatedAt']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no property requests yet.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Braj Property. All Rights Reserved.</p>
    </footer>
</body>
</html>

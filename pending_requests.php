<?php
// pending_requests.php – Admin review and apply property change requests
include 'header.php';
require_once 'db_connection.php';

// Only Admins can access
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Handle Approve/Reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $requestId = (int)$_POST['request_id'];
    $approve   = isset($_POST['approve']);

    // Fetch the pending request
    $stmt = $conn->prepare(
        "SELECT Action, Payload, AgentID, PropertyID
         FROM property_requests
         WHERE RequestID = ? AND Status = 'PENDING'"
    );
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $action   = $row['Action'];
        $payload  = json_decode($row['Payload'], true);
        $agentId  = (int)$row['AgentID'];
        $propId   = $row['PropertyID'] ? (int)$row['PropertyID'] : null;

        if ($approve) {
            // ===== ADD Action =====
            if ($action === 'ADD') {
                $desc     = $conn->real_escape_string($payload['Name'] ?? '');
                $price    = floatval($payload['Price'] ?? 0);
                $size     = intval($payload['Size'] ?? 0);
                $image    = $conn->real_escape_string($payload['Image'] ?? '');
                // Lookup or insert LocationID
                $locEsc   = $conn->real_escape_string($payload['Location'] ?? '');
                $locRes   = $conn->query("SELECT LocationID FROM location WHERE City = '$locEsc' LIMIT 1");
                if ($locRes && $locRes->num_rows) {
                    $locRow     = $locRes->fetch_assoc();
                    $locationId = (int)$locRow['LocationID'];
                } else {
                    $insLoc     = $conn->prepare("INSERT INTO location (City) VALUES (?)");
                    $insLoc->bind_param('s', $locEsc);
                    $insLoc->execute();
                    $locationId = (int)$conn->insert_id;
                    $insLoc->close();
                }
                // Insert property
                $ins = $conn->prepare(
                    "INSERT INTO property (SalePrice, Size, Description, image, AgentID, LocationID)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $ins->bind_param('dissii', $price, $size, $desc, $image, $agentId, $locationId);
                $ins->execute();
                $ins->close();

            // ===== EDIT Action =====
            } elseif ($action === 'EDIT' && $propId) {
                $fields = [];
                $types  = '';
                $values = [];
                if (isset($payload['Description'])) {
                    $fields[] = 'Description = ?'; $types .= 's'; $values[] = $payload['Description'];
                }
                if (isset($payload['Price'])) {
                    $fields[] = 'SalePrice = ?';   $types .= 'd'; $values[] = floatval($payload['Price']);
                }
                if (isset($payload['Size'])) {
                    $fields[] = 'Size = ?';        $types .= 'i'; $values[] = intval($payload['Size']);
                }
                if (isset($payload['Location'])) {
                    $locEsc   = $conn->real_escape_string($payload['Location']);
                    $locRes   = $conn->query("SELECT LocationID FROM location WHERE City = '$locEsc' LIMIT 1");
                    if ($locRes && $locRes->num_rows) {
                        $locRow     = $locRes->fetch_assoc();
                        $locationId = (int)$locRow['LocationID'];
                    } else {
                        $insLoc     = $conn->prepare("INSERT INTO location (City) VALUES (?)");
                        $insLoc->bind_param('s', $locEsc);
                        $insLoc->execute();
                        $locationId = (int)$conn->insert_id;
                        $insLoc->close();
                    }
                    $fields[] = 'LocationID = ?';   $types .= 'i'; $values[] = $locationId;
                }
                if (isset($payload['Image'])) {
                    $fields[] = 'image = ?';        $types .= 's'; $values[] = $payload['Image'];
                }
                if ($fields) {
                    $types .= 'i'; $values[] = $propId;
                    $sql = "UPDATE property SET " . implode(', ', $fields) . " WHERE PropertyID = ?";
                    $upd = $conn->prepare($sql);
                    $upd->bind_param($types, ...$values);
                    $upd->execute();
                    $upd->close();
                }

            // ===== DELETE Action =====
            } elseif ($action === 'DELETE' && $propId) {
                $del = $conn->prepare("DELETE FROM property WHERE PropertyID = ?");
                $del->bind_param('i', $propId);
                $del->execute();
                $del->close();
            }
            $status = 'APPROVED';
        } else {
            $status = 'REJECTED';
        }

        // Mark the request as reviewed
        $upd = $conn->prepare(
            "UPDATE property_requests
             SET Status = ?, ReviewedAt = NOW(), ReviewerID = ?
             WHERE RequestID = ?"
        );
        $upd->bind_param('sii', $status, $meId, $requestId);
        $upd->execute();
        $upd->close();
    }

    header('Location: pending_requests.php');
    exit;
}

// Fetch all pending requests for display
$result = $conn->query(
    "SELECT r.RequestID, r.Action, r.Payload, u.Name AS AgentName, r.CreatedAt
     FROM property_requests r
     JOIN user u ON r.AgentID = u.UserID
     WHERE r.Status = 'PENDING'
     ORDER BY r.CreatedAt DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Requests – Admin</title>
  <link rel="stylesheet" href="pending_requests.css">
</head>
<body>
  <header>
    <h1>Pending Property Requests</h1>
    <nav><a href="admin_dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a></nav>
  </header>
  <main>
    <?php if ($result && $result->num_rows): ?>
      <table>
        <thead>
          <tr><th>ID</th><th>Agent</th><th>Type</th><th>Data</th><th>Submitted</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['RequestID'] ?></td>
              <td><?= htmlspecialchars($row['AgentName']) ?></td>
              <td><?= htmlspecialchars($row['Action']) ?></td>
              <td><pre><?= htmlspecialchars(json_encode(json_decode($row['Payload']), JSON_PRETTY_PRINT)) ?></pre></td>
              <td><?= htmlspecialchars($row['CreatedAt']) ?></td>
              <td>
                <form method="post" style="display:inline">
                  <input type="hidden" name="request_id" value="<?= $row['RequestID'] ?>">
                  <button name="approve">Approve</button>
                </form>
                <form method="post" style="display:inline">
                  <input type="hidden" name="request_id" value="<?= $row['RequestID'] ?>">
                  <button name="reject">Reject</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No pending requests.</p>
    <?php endif; ?>
  </main>
</body>
</html>

<?php
// pending_requests.php – Admin view of pending property requests with AJAX-enabled Approve/Reject + CRUD
require_once 'db_connection.php';
session_start();
include 'header.php';    // provides isAdmin(), isLoggedIn(), etc.

// Only Admins
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Helper: find-or-create LocationID
function getLocationId($city, $conn) {
    $q = $conn->prepare("SELECT LocationID FROM location WHERE City = ?");
    $q->bind_param('s', $city);
    $q->execute();
    $res = $q->get_result()->fetch_assoc();
    $q->close();
    if ($res) return (int)$res['LocationID'];
    $ins = $conn->prepare("INSERT INTO location (City) VALUES (?)");
    $ins->bind_param('s', $city);
    $ins->execute();
    $newId = $ins->insert_id;
    $ins->close();
    return $newId;
}

// AJAX handler: process request and return JSON
// 🟢 AJAX handler: approve or reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ajax'])) {
    header('Content-Type: application/json');
    $rid           = (int)($_POST['request_id'] ?? 0);
    $adminDecision = $_POST['action'] ?? '';
    $success       = false;

    if ($adminDecision === 'reject') {
        // mark as rejected only
        $stmt = $conn->prepare(
          "UPDATE property_requests SET Status='REJECTED', ReviewedAt=NOW(), ReviewerID=? WHERE RequestID=?"
        );
        $stmt->bind_param('ii', $_SESSION['user_id'], $rid);
        $success = $stmt->execute();
        $stmt->close();
    }
    elseif ($adminDecision === 'approve') {
        // fetch pending request
        $stmt = $conn->prepare(
          "SELECT Action, Payload, AgentID, PropertyID FROM property_requests WHERE RequestID=? AND Status='PENDING'"
        );
        $stmt->bind_param('i', $rid);
        $stmt->execute();
        $req = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($req) {
            $type    = $req['Action'];
            $data    = json_decode($req['Payload'], true) ?: [];
            $agentId = (int)$req['AgentID'];
            $propId  = $req['PropertyID'] ? (int)$req['PropertyID'] : null;
            switch ($type) {
                case 'ADD':
                    $price = $data['Price'] ?? 0;
                    $size  = $data['Size'] ?? 0;
                    $desc  = $data['Description'] ?? ($data['Name'] ?? '');
                    $img   = $data['Image'] ?? null;
                    $loc   = isset($data['Location']) ? getLocationId($data['Location'], $conn) : null;
                    $i = $conn->prepare("INSERT INTO property (SalePrice, Size, Description, image, AgentID, LocationID) VALUES (?,?,?,?,?,?)");
                    $i->bind_param('dissii', $price, $size, $desc, $img, $agentId, $loc);
                    $success = $i->execute();
                    $i->close();
                    break;
                case 'EDIT':
                    if ($propId) {
                        $fields = [];
                        $types  = '';
                        $vals   = [];
                        if (isset($data['Price']))      { $fields[]='SalePrice=?'; $types.='d'; $vals[]=$data['Price']; }
                        if (isset($data['Size']))       { $fields[]='Size=?';      $types.='i'; $vals[]=$data['Size']; }
                        if (isset($data['Description'])||isset($data['Name'])) { $fields[]='Description=?'; $types.='s'; $vals[]=$data['Description']??$data['Name']; }
                        if (isset($data['Image']))      { $fields[]='image=?';     $types.='s'; $vals[]=$data['Image']; }
                        if (isset($data['Location']))   { $loc=getLocationId($data['Location'],$conn); $fields[]='LocationID=?'; $types.='i'; $vals[]=$loc; }
                        if ($fields) {
                            $sql = "UPDATE property SET " . implode(',', $fields) . " WHERE PropertyID=?";
                            $types   .= 'i';
                            $vals[]   = $propId;
                            $u = $conn->prepare($sql);
                            $u->bind_param($types, ...$vals);
                            $success = $u->execute();
                            $u->close();
                        }
                    }
                    break;
                case 'DELETE':
                    if ($propId) {
                        $n = $conn->prepare("UPDATE property_requests SET PropertyID=NULL WHERE PropertyID=?");
                        $n->bind_param('i',$propId); $n->execute(); $n->close();
                        $d = $conn->prepare("DELETE FROM property WHERE PropertyID=?");
                        $d->bind_param('i',$propId); $success = $d->execute(); $d->close();
                    }
                    break;
            }
            if ($success) {
                $s = $conn->prepare("UPDATE property_requests SET Status='APPROVED', ReviewedAt=NOW(), ReviewerID=? WHERE RequestID=?");
                $s->bind_param('ii', $_SESSION['user_id'], $rid);
                $s->execute();
                $s->close();
            }
        }
    }

    echo json_encode(['success'=>$success,'request_id'=>$rid]);
    exit;
}
// Fetch pending requests
$sql = "
SELECT r.RequestID, u.Name AS AgentName, r.Action, r.Payload, r.CreatedAt
  FROM property_requests r
  JOIN user u ON r.AgentID=u.UserID
 WHERE r.Status='PENDING'
 ORDER BY r.CreatedAt DESC
";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pending Requests &mdash; Braj Property Admin</title>
  <link rel="stylesheet" href="pending_requests.css">
  <style>
    .actions { display: flex; gap: 0.5rem; }
  </style>
</head>
<body>
  <header class="site-container">
    <h1>Pending Requests</h1>
    <nav><a href="admin_dashboard.php">← Dashboard</a> <a href="logout.php" class="btn-reject">Logout</a></nav>
  </header>
  <main class="site-container">
  <?php if ($res && $res->num_rows > 0): ?>
    <table>
      <thead><tr>
        <th>ID</th><th>Agent</th><th>Type</th><th>Details</th><th>Submitted</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php while ($r = $res->fetch_assoc()): ?>
        <tr id="row-<?= $r['RequestID'] ?>">
          <td><?= $r['RequestID'] ?></td>
          <td><?= htmlspecialchars($r['AgentName']) ?></td>
          <td><?= htmlspecialchars($r['Action']) ?></td>
          <td><pre><?= htmlspecialchars(json_encode(json_decode($r['Payload']), JSON_PRETTY_PRINT)) ?></pre></td>
          <td><?= date('Y-m-d H:i', strtotime($r['CreatedAt'])) ?></td>
          <td><div class="actions">
            <button class="btn-approve ajax-action" data-id="<?= $r['RequestID'] ?>" data-action="approve">Approve</button>
            <button class="btn-reject ajax-action"  data-id="<?= $r['RequestID'] ?>" data-action="reject">Reject</button>
          </div></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="no-requests">No pending requests at this time.</p>
  <?php endif; ?>
  </main>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(function(){
      $('.ajax-action').click(function(){
        var btn = $(this), id = btn.data('id'), action = btn.data('action');
        $.post('pending_requests.php', {ajax:1, request_id:id, action:action}, function(resp){
          if (resp.success) $('#row-'+resp.request_id).fadeOut(300, function(){ $(this).remove(); });
          else alert('Failed to '+action+' request #'+id);
        }, 'json').fail(function(){ alert('Network error on '+action); });
      });
    });
  </script>
</body>
</html>

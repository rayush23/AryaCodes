<?php
// delete_request.php – Agent submits a DELETE request for admin approval
include 'header.php';
require_once 'db_connection.php';

// Only Agents (and Admins) can access
if (!isAgent() || empty($_GET['PropertyID'])) {
    header('Location: agent_profile.php');
    exit;
}

$propId = (int)$_GET['PropertyID'];
$agentId = $meId;

// Insert DELETE request
$sql = "INSERT INTO property_requests (AgentID, PropertyID, Action, Payload)
        VALUES (?, ?, 'DELETE', '{}')";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $agentId, $propId);
$stmt->execute();
$stmt->close();

header('Location: agent_profile.php?success=Delete+request+submitted');
exit;
?>

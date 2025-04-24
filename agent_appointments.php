<?php
// agent_appointments.php – Agent dashboard: list and create appointments
include 'header.php';
require_once 'db_connection.php';

// Only Agents can access
if (!isAgent()) {
    header('Location: login.php');
    exit;
}

$agentId = $meId; // from session via header.php
$message = '';
// Handle new appointment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_appt'])) {
    $clientId   = (int) $_POST['client'];
    $propertyId = (int) $_POST['property'];
    $datetime   = $_POST['datetime']; // format: YYYY-MM-DDTHH:MM
    $dt         = DateTime::createFromFormat('Y-m-d\TH:i', $datetime);
    if ($dt) {
        $date = $dt->format('Y-m-d');
        $time = $dt->format('H:i:s');
        // Insert appointment
        $ins = $conn->prepare(
            "INSERT INTO appointment (Date, Time, ClientID, PropertyID, AgentID)
             VALUES (?, ?, ?, ?, ?)"
        );
        $ins->bind_param('ssiii', $date, $time, $clientId, $propertyId, $agentId);
        if ($ins->execute()) {
            $message = 'Appointment created successfully.';
        } else {
            $message = 'Error creating appointment: ' . $ins->error;
        }
        $ins->close();
    } else {
        $message = 'Invalid date/time format.';
    }
}

// Fetch Clients list
$clients = $conn->query(
    "SELECT c.ClientID, u.Name
       FROM client c
       JOIN user u ON c.ClientID = u.UserID"
);
// Fetch this agent's properties
$propStmt = $conn->prepare(
    "SELECT PropertyID, Description
       FROM property
       WHERE AgentID = ?"
);
$propStmt->bind_param('i', $agentId);
$propStmt->execute();
$properties = $propStmt->get_result();
$propStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="agent_profile.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1>My Upcoming Appointments</h1>
        <nav>
    <ul>
        <li><a href="agent_profile.php">Dashboard</a></li>
        <li><a href="agent_appointments.php">Appointments</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
    </header>

    <main>
        <!-- New Appointment Form -->
        <section class="create-appt">
            <h2>Schedule New Appointment</h2>
            <?php if ($message): ?>
                <div class="msg"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="create_appt" value="1">
                <label>Client:
                    <select name="client" required>
                        <option value="">Select Client</option>
                        <?php while ($c = $clients->fetch_assoc()): ?>
                            <option value="<?= $c['ClientID'] ?>"><?= htmlspecialchars($c['Name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </label>
                <label>Property:
                    <select name="property" required>
                        <option value="">Select Property</option>
                        <?php while ($p = $properties->fetch_assoc()): ?>
                            <option value="<?= $p['PropertyID'] ?>"><?= htmlspecialchars($p['Description']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </label>
                <label>Date & Time:
                    <input type="datetime-local" name="datetime" required>
                </label>
                <button type="submit">Create Appointment</button>
            </form>
        </section>

        <!-- Appointments Table -->
        <section class="list-appt">
            <h2>Upcoming Appointments</h2>
            <table id="appt-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Client</th>
                        <th>Property</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4">Loading appointments...</td></tr>
                </tbody>
            </table>
        </section>
    </main>

    <script>
    $(function loadAppointments() {
        $.ajax({
            url: 'api/appointments.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var $tbody = $('#appt-table tbody').empty();
                if (!data.length) {
                    $tbody.append('<tr><td colspan="4">No upcoming appointments.</td></tr>');
                } else {
                    data.forEach(function(a) {
                        $tbody.append(
                            '<tr>' +
                            '<td>' + a.Date       + '</td>' +
                            '<td>' + a.Time       + '</td>' +
                            '<td>' + a.ClientName + '</td>' +
                            '<td>' + a.Property   + '</td>' +
                            '</tr>'
                        );
                    });
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Error';
                $('#appt-table tbody').html('<tr><td colspan="4">'+ msg +'</td></tr>');
            }
        });
    });
    </script>
</body>
</html>


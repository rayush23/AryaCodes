<?php
session_start();
include 'db_connection.php';

// Ensure database connection is active
if (!$conn || $conn->connect_error) {
    die('Connection to database failed: ' . $conn->connect_error);
}

// Fetch agents by joining the user and agent tables
$sql = "SELECT user.Name, user.Email, user.PhoneNumber, agent.LicenseNumber 
        FROM user 
        INNER JOIN agent ON user.UserID = agent.AgentID 
        WHERE user.UserType = 'Agent'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agents - Braj Property</title>
    <link rel="stylesheet" href="agent.css">
</head>

<body>
    <header>
        <div class="logo">
            <h1>Braj Property</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="properties.php">Properties</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section class="agents-section">
        <h2>Available Agents</h2>

        <div class="agents-list">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each agent
                while ($agent = $result->fetch_assoc()) {
                    echo "<div class='agent-card'>";
                    echo "<h3>" . htmlspecialchars($agent['Name']) . "</h3>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($agent['Email']) . "</p>";
                    echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($agent['PhoneNumber']) . "</p>";
                    echo "<p><strong>License Number:</strong> " . htmlspecialchars($agent['LicenseNumber']) . "</p>";

                    // Mailto button for emailing the agent via Gmail
                    echo "<a href='mailto:" . htmlspecialchars($agent['Email']) . "?subject=Inquiry about properties' target='_blank' class='mail-button'>Email Agent</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No agents available at the moment.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Braj Property. All Rights Reserved.</p>
    </footer>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>
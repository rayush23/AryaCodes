<?php
// admin_appointments.php
include 'header.php'; // purely PHP: session_start() guard + isAdmin(), etc.

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Appointment Queue';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
  <header>
    <h1>Admin Panel</h1>
    <nav>
      <a href="admin_dashboard.php">Dashboard</a> |
      <a href="admin_appointments.php">Appointment Queue</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <h2><?= htmlspecialchars($pageTitle) ?></h2>
    <table id="admin-appt-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Time</th>
          <th>Client</th>
          <th>Property</th>
          <th>Agent</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- rows will be injected here -->
      </tbody>
    </table>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  // Wrap in a function so we can reload on cancel
  function loadAdminAppts() {
    $.ajax({
      url: '/brajproperty/api/admin_appointments.php',
      method: 'GET',
      dataType: 'json',
      success: function(appts) {
        var $tbody = $('#admin-appt-table tbody').empty();
        if (!appts.length) {
          $tbody.append('<tr><td colspan="6">No appointments found.</td></tr>');
          return;
        }
        appts.forEach(function(a) {
          $tbody.append(
            '<tr data-id="'+ a.AppointmentID +'">' +
              '<td>'+ a.Date       +'</td>' +
              '<td>'+ a.Time       +'</td>' +
              '<td>'+ a.ClientName +'</td>' +
              '<td>'+ a.Property   +'</td>' +
              '<td>'+ a.AgentName  +'</td>' +
              '<td>' +
                '<button class="cancel-btn">Cancel</button>' +
              '</td>' +
            '</tr>'
          );
        });
      },
      error: function(xhr) {
        alert('Error loading appointments: HTTP '+ xhr.status);
      }
    });
  }

  $(function(){
    loadAdminAppts();

    // Delegate cancel click
    $('#admin-appt-table').on('click', '.cancel-btn', function(){
      if (!confirm('Really cancel this appointment?')) return;
      var id = $(this).closest('tr').data('id');
      $.post(
        '/brajproperty/api/admin_delete_appointment.php',
        { AppointmentID: id },
        function(resp) {
          if (resp.success) {
            loadAdminAppts();
          } else {
            alert('Cancel failed: ' + resp.error);
          }
        },
        'json'
      );
    });
  });
  </script>
</body>
</html>

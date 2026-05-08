<?php include 'includes/report_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Performance Report</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<main class="container">
    <h2>Hospital Performance Report</h2>
    <div class="stats-grid">
        <div class="stat-box">
            <h4>Total Appointments Today</h4>
            <p class="count"><?php echo $todayCount; ?></p>
        </div>
        <div class="stat-box">
            <h4>Active Specialists</h4>
            <p class="count"><?php echo $doctorCount; ?></p>
        </div>
    </div>

    <h3>Detailed Appointment Log</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Status</th>
                <th>Fee</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>$<?php echo number_format(($row['amount'] ?? 0), 2); ?></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
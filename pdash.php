<?php
session_start();
require_once 'includes/db.php'; // Database connection

// 1. Session confirmation and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 2. getting list of doctors
try {
    $stmt_docs = $pdo->query("SELECT doctor_id, name, specialization FROM doctors");
    $doctors = $stmt_docs->fetchAll();
} catch (PDOException $e) {
    die("Error fetching doctors: " . $e->getMessage());
}

// getting patient history
// using database view
try {
    $stmt_app = $pdo->prepare("SELECT * FROM vw_appointmentsummary WHERE patient_name = (SELECT username FROM users WHERE user_id = ?)");
    $stmt_app->execute([$patient_id]);
    $my_appointments = $stmt_app->fetchAll();
} catch (PDOException $e) {
    die("Error fetching appointments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard - City Care Hospital</title>
    <link rel="stylesheet" href="style-p.css">
</head>
<body>
    <nav class="main-nav">
        <div class="logo">City Care Hospital</div>
        <ul class="nav-links">
            <li><a href="">Dashboard</a></li>
            <li><a href="my_appointments.php">My Appointments</a></li>
            <li><a href="includes/logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="container">
        <h2>Welcome Patient <?php echo htmlspecialchars($username); ?></h2>

        <section class="booking-card">
            <h3>Book a New Appointment</h3>
            <form action="includes/aphandler.php" method="POST">
                <div class="input-group">
                    <label>Select Specialist</label>
                    <select name="doctor_id" required>
                        <option value="">-- Choose Doctor --</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?php echo $doc['doctor_id']; ?>">
                                <?php echo $doc['name'] . " (" . $doc['specialization'] . ")"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Preferred Date</label>
                    <input type="date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <button type="submit" name="book_now">Confirm Booking</button>
            </form>
        </section>

        <section class="report-section">
            <h3>My Appointment History</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($my_appointments) > 0): ?>
                        <?php foreach ($my_appointments as $app): ?>
                            <tr>
                                <td>#<?php echo $app['appointment_id']; ?></td>
                                <td><?php echo $app['doctor_name']; ?></td>
                                <td><?php echo $app['appointment_date']; ?></td>
                                <td><span class="status-badge"><?php echo $app['status']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>a
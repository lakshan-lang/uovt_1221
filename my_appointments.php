<?php
session_start();

require_once 'includes/db.php';

// Verify user authentication and authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$status_message = "";

/* =========================
   APPOINTMENT CANCELLATION
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {

    $appointment_id = $_POST['appointment_id'];

    try {

        $cancel_sql = "UPDATE appointments 
                       SET status = 'Cancelled'
                       WHERE appointment_id = ? 
                       AND patient_id = ?";

        $cancel_stmt = $pdo->prepare($cancel_sql);

        if ($cancel_stmt->execute([$appointment_id, $patient_id])) {

            $status_message = "
                <div class='alert success'>
                    Appointment cancelled successfully.
                </div>
            ";

        } else {

            $status_message = "
                <div class='alert error'>
                    Failed to cancel appointment.
                </div>
            ";
        }

    } catch (PDOException $e) {

        $status_message = "
            <div class='alert error'>
                System Error: " . $e->getMessage() . "
            </div>
        ";
    }
}

/* =========================
   FETCH APPOINTMENTS
========================= */

try {

    $fetch_sql = "
        SELECT appointment_id,
               doctor_name,
               appointment_date,
               status
        FROM vw_appointmentsummary
        WHERE patient_name = (
            SELECT username 
            FROM users 
            WHERE user_id = ?
        )
        ORDER BY appointment_date DESC
    ";

    $stmt = $pdo->prepare($fetch_sql);
    $stmt->execute([$patient_id]);

    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Data Retrieval Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments | City Care Hospital</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Segoe UI',sans-serif;
            background:linear-gradient(135deg,#e9f0f5 0%,#d4e0e8 100%);
            min-height:100vh;
            color:#1a2a3a;
        }

        /* =========================
           NAVBAR
        ========================= */

        .main-nav{
            background:linear-gradient(90deg,#0b3b5f 0%,#1b5a7a 100%);
            padding:1rem 2rem;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 4px 20px rgba(0,0,0,0.15);
            position:sticky;
            top:0;
            z-index:100;
        }

        .logo{
            font-size:1.7rem;
            font-weight:bold;
            color:white;
            background:rgba(255,255,255,0.1);
            padding:0.4rem 1rem;
            border-radius:40px;
        }

        .nav-links{
            list-style:none;
            display:flex;
            gap:1rem;
        }

        .nav-links li a{
            color:white;
            text-decoration:none;
            padding:10px 16px;
            border-radius:30px;
            font-weight:600;
            transition:0.3s;
        }

        .nav-links li a:hover{
            background:#ffb347;
            color:#1a2a3a;
        }

        /* =========================
           MAIN CONTAINER
        ========================= */

        .container{
            max-width:1300px;
            margin:2rem auto;
            padding:0 2rem;
        }

        .page-card{
            background:white;
            border-radius:25px;
            padding:2rem;
            box-shadow:0 15px 35px rgba(0,0,0,0.12);
        }

        /* =========================
           TITLE
        ========================= */

        h2{
            font-size:2rem;
            color:#0b3b5f;
            margin-bottom:1.5rem;
            border-bottom:4px solid #ffb347;
            display:inline-block;
            padding-bottom:8px;
        }

        /* =========================
           ALERTS
        ========================= */

        .alert{
            padding:14px;
            border-radius:10px;
            margin-bottom:20px;
            font-weight:bold;
        }

        .success{
            background:#d4edda;
            color:#155724;
        }

        .error{
            background:#f8d7da;
            color:#721c24;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper{
            overflow-x:auto;
        }

        .data-table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
            overflow:hidden;
            border-radius:18px;
            background:white;
        }

        .data-table thead{
            background:#0b3b5f;
            color:white;
        }

        .data-table th{
            padding:16px;
            text-align:left;
        }

        .data-table td{
            padding:16px;
            border-bottom:1px solid #e6edf2;
        }

        .data-table tbody tr:hover{
            background:#fff6e8;
            transition:0.2s;
        }

        /* =========================
           STATUS BADGES
        ========================= */

        .status-badge{
            padding:6px 14px;
            border-radius:30px;
            font-size:0.85rem;
            font-weight:bold;
            text-transform:uppercase;
        }

        .scheduled{
            background:#dbefff;
            color:#0b63b6;
        }

        .cancelled{
            background:#ffe0e0;
            color:#b32626;
        }

        .pending{
            background:#fff3cd;
            color:#856404;
        }

        .confirmed{
            background:#d4edda;
            color:#155724;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn-cancel{
            background:linear-gradient(90deg,#ff5252,#d32f2f);
            color:white;
            border:none;
            padding:10px 18px;
            border-radius:30px;
            cursor:pointer;
            font-weight:bold;
            transition:0.3s;
        }

        .btn-cancel:hover{
            transform:scale(1.05);
            box-shadow:0 5px 15px rgba(255,82,82,0.3);
        }

        /* =========================
           EMPTY STATE
        ========================= */

        .empty-row{
            text-align:center;
            color:#777;
            padding:40px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media(max-width:768px){

            .main-nav{
                flex-direction:column;
                gap:1rem;
            }

            .nav-links{
                flex-wrap:wrap;
                justify-content:center;
            }

            .container{
                padding:1rem;
            }

            .data-table th,
            .data-table td{
                padding:10px;
                font-size:0.9rem;
            }

            h2{
                font-size:1.5rem;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <nav class="main-nav">

        <div class="logo">
            City Care Hospital
        </div>

        <ul class="nav-links">
            <li><a href="pdash.php">Dashboard</a></li>
            <li><a href="my_appointments.php">My Appointments</a></li>
            <li><a href="includes/logout.php">Logout</a></li>
        </ul>

    </nav>

    <!-- MAIN CONTENT -->

    <main class="container">

        <div class="page-card">

            <h2>My Appointment History</h2>

            <?php echo $status_message; ?>

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>
                    <tr>
    <th>Appointment No</th>
    <th>Doctor</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>
                    </thead>

                    <tbody>

                        <?php if(count($appointments) > 0): ?>

                            <?php foreach($appointments as $row): ?>

                               <tr>

    <td>
        <strong>
            AP-<?php echo str_pad($row['appointment_id'], 4, "0", STR_PAD_LEFT); ?>
        </strong>
    </td>

    <td>
        <strong>
            Dr. <?php echo htmlspecialchars($row['doctor_name']); ?>
        </strong>
    </td>

    <td>
        <?php echo htmlspecialchars($row['appointment_date']); ?>
    </td>

    <td>
        <?php echo $row['appointment_time'] ?? 'Not Set'; ?>
    </td>

    <td>

        <span class="status-badge <?php echo strtolower($row['status']); ?>">

            <?php echo htmlspecialchars($row['status']); ?>

        </span>

    </td>

    <td>

        <?php if($row['status'] == 'Pending'): ?>

            <span style="color:orange;font-weight:bold;">
                Waiting For Confirmation
            </span>

        <?php elseif($row['status'] == 'Confirmed'): ?>

            <span style="color:green;font-weight:bold;">

                Appointment Confirmed

            </span>

        <?php else: ?>

            <span style="color:red;font-weight:bold;">
                Cancelled
            </span>

        <?php endif; ?>

    </td>

</tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="4" class="empty-row">

                                    No appointment records found.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>
</html>
<?php
session_start();
include "../db/db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

// Fetch statistics
$query_total_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($query_total_users);
$total_users = $result_users->fetch_assoc()["total_users"];

$query_total_complaints = "SELECT COUNT(*) AS total_complaints FROM complaints";
$result_complaints = $conn->query($query_total_complaints);
$total_complaints = $result_complaints->fetch_assoc()["total_complaints"];

$query_pending_complaints = "SELECT COUNT(*) AS pending_complaints FROM complaints WHERE status = 'Pending'";
$result_pending = $conn->query($query_pending_complaints);
$pending_complaints = $result_pending->fetch_assoc()["pending_complaints"];

// Fetch complaints details
$query_complaints = "SELECT complaint_id, description, department_id FROM complaints";
$result_complaints_table = $conn->query($query_complaints);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s;
            width: 30%;
            text-align: center;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn {
            background: #ff7e5f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }
        .btn:hover {
            background: #feb47b;
        }
        /* Table Styling */
        .complaints-table {
            width: 100%;
            margin-top: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background: rgba(255, 255, 255, 0.2);
        }
        tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.1);
        }
        .assign-btn {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .assign-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, Admin</h1>
        <div class="stats">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h3>Total Complaints</h3>
                <p><?php echo $total_complaints; ?></p>
            </div>
            <div class="card">
                <h3>Pending Complaints</h3>
                <p><?php echo $pending_complaints; ?></p>
            </div>
        </div>
        <div class="actions">
            <a href="manage_users.php" class="btn">Manage Users</a>
            <a href="../complaints/assign_complaint.php" class="btn">Assign Complaints</a>
            <a href="view_reports.php" class="btn">View Reports</a>
            <a href="../auth/logout.php" class="btn logout">Logout</a>
        </div>

        <!-- Complaints Table -->
        <div class="complaints-table">
            <h2 style="text-align: center; margin-top: 20px;">Registered Complaints</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Description</th>
                        <th>Department</th>
                        <th>Assign Official</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_complaints_table->num_rows > 0) {
                        while ($row = $result_complaints_table->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["complaint_id"] . "</td>";
                            echo "<td>" . $row["description"] . "</td>";
                            echo "<td>" . $row["department_id"] . "</td>";
                            echo "<td><a href='../complaints/assign_complaint.php?complaint_id=" . $row["complaint_id"] . "' class='assign-btn'>Assign Official</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No complaints found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

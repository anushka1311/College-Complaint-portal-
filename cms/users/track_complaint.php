<?php
session_start();
include "../db/db_connect.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/user_login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user complaints
$query = "SELECT c.complaint_id, d.department_name, c.description, c.status, o.name AS official_name 
          FROM complaints c
          LEFT JOIN departments d ON c.department_id = d.department_id
          LEFT JOIN officials o ON c.assigned_to = o.official_id
          WHERE c.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Complaint</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            color: #fff;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .complaint-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            text-align: left;
        }
        .status {
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
        }
        .pending { background: orange; color: white; }
        .in-progress { background: blue; color: white; }
        .resolved { background: green; color: white; }
        .withdrawn { background: gray; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Track Your Complaints</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="complaint-box">
                    <p><strong>Complaint ID:</strong> <?php echo $row['complaint_id']; ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($row['department_name']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong>Assigned Official:</strong> <?php echo $row['official_name'] ? htmlspecialchars($row['official_name']) : 'Not Assigned'; ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status <?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                            <?php echo $row['status'] === 'withdrawn' ? 'Withdrawn' : $row['status']; ?>
                        </span>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No complaints found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $stmt->close(); $conn->close(); ?>

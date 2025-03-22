<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/user_login.php");
    exit();
}

include "../db/db_connect.php"; 

$user_id = $_SESSION["user_id"];
$query = "SELECT full_name, email, department FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch user complaints
$complaints_query = "SELECT complaint_id, title, status, created_at FROM complaints WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($complaints_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$complaints = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($user["full_name"]); ?>!</h2>
        <p>Email: <?php echo htmlspecialchars($user["email"]); ?></p>
        <p>Department: <?php echo htmlspecialchars($user["department"]); ?></p>

        <a href="submit_complaint.php" class="btn btn-primary">Submit Complaint</a>
        <a href="track_complaint.php" class="btn btn-secondary">Track Complaint</a>
        <a href="feedback.php" class="btn btn-success">Give Feedback</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>

        <h3 class="mt-4">Your Complaints</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $complaints->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                        <td><?php echo htmlspecialchars($row["status"]); ?></td>
                        <td><?php echo $row["created_at"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

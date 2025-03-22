<?php
session_start();
include "../db/db_connect.php"; // Database connection

// Check if the official is logged in
if (!isset($_SESSION["official_id"])) {
    header("Location: ../officials/official_login.php");
    exit();
}

$official_id = $_SESSION["official_id"];
$official_name = $_SESSION["official_name"];
$official_role = $_SESSION["official_role"];

// Fetch official details
$query = "SELECT name, email, department, role FROM officials WHERE official_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $official_id);
$stmt->execute();
$stmt->bind_result($name, $email, $department, $role);
$stmt->fetch();
$stmt->close();

// Fetch complaints assigned to the official
$query = "SELECT complaint_id, user_id, description, status, official_status FROM complaints WHERE assigned_official = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $official_id);
$stmt->execute();
$result = $stmt->get_result();
$complaints = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        .complaint { border-bottom: 1px solid #ddd; padding: 10px; }
        .btn { padding: 5px 10px; cursor: pointer; border: none; border-radius: 5px; }
        .accept { background: green; color: white; }
        .deny { background: red; color: white; }
        .resolve { background: blue; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $official_name; ?> (<?php echo $official_role; ?>)</h2>
        <p>Email: <?php echo $email; ?></p>
        <p>Department: <?php echo $department; ?></p>

        <h3>Assigned Complaints</h3>
        <?php if (empty($complaints)) { ?>
            <p>No complaints assigned to you.</p>
        <?php } else { ?>
            <?php foreach ($complaints as $complaint) { ?>
                <div class="complaint">
                    <p><strong><?php echo $complaint['title']; ?></strong></p>
                    <p><?php echo $complaint['description']; ?></p>
                    <p><strong>Status:</strong> <?php echo $complaint['official_status']; ?></p>

                    <?php if ($complaint['official_status'] == 'Pending') { ?>
                        <button class="btn accept" onclick="updateComplaint(<?php echo $complaint['complaint_id']; ?>, 'Accepted')">Accept</button>
                        <button class="btn deny" onclick="updateComplaint(<?php echo $complaint['complaint_id']; ?>, 'Denied')">Deny</button>
                    <?php } elseif ($complaint['official_status'] == 'Accepted') { ?>
                        <button class="btn resolve" onclick="resolveComplaint(<?php echo $complaint['complaint_id']; ?>)">Resolve</button>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <script>
        function updateComplaint(complaintId, status) {
            fetch('update_complaint_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ complaint_id: complaintId, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Complaint " + status + " successfully!");
                    location.reload();
                } else {
                    alert("Error updating complaint.");
                }
            });
        }

        function resolveComplaint(complaintId) {
            let remarks = prompt("Enter remarks for resolution:");
            if (remarks) {
                fetch('resolve_complaint.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ complaint_id: complaintId, remarks: remarks })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Complaint resolved successfully!");
                        location.reload();
                    } else {
                        alert("Error resolving complaint.");
                    }
                });
            }
        }
    </script>
</body>
</html>

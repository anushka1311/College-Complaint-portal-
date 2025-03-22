<?php
session_start();
$_SESSION['admin_logged_in'] = true;
require_once "../db/db_connect.php"; // Database connection

// Ensure the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

// Fetch unassigned complaints
$complaintsQuery = "SELECT complaint_id, department_id, description, status FROM complaints WHERE assigned_to IS NULL";
$complaintsResult = $conn->query($complaintsQuery);

// Fetch available officials
$officialsQuery = "SELECT official_id, name FROM officials";
$officialsResult = $conn->query($officialsQuery);

// Assign complaint if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = $_POST['complaint_id'];
    $official_id = $_POST['official_id'];

    $assignQuery = "UPDATE complaints SET assigned_to = ?, status = 'In Progress' WHERE complaint_id = ?";
    $stmt = $conn->prepare($assignQuery);
    $stmt->bind_param("ii", $official_id, $complaint_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Complaint assigned successfully!";
    } else {
        $_SESSION['message'] = "Failed to assign complaint.";
    }
    $stmt->close();
    header("Location: assign_complaint.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Complaint</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Adjust CSS as needed -->
</head>
<body>
    <h2>Assign Complaints</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="complaint_id">Select Complaint:</label>
        <select name="complaint_id" required>
            <?php while ($row = $complaintsResult->fetch_assoc()): ?>
                <option value="<?php echo $row['complaint_id']; ?>">
                    [<?php echo $row['department_id']; ?>] - <?php echo substr($row['description'], 0, 50); ?>...
                </option>
            <?php endwhile; ?>
        </select>

        <label for="official_id">Assign To:</label>
        <select name="official_id" required>
            <?php while ($row = $officialsResult->fetch_assoc()): ?>
                <option value="<?php echo $row['official_id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Assign Complaint</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>

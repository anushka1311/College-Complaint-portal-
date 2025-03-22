<?php
session_start();
include "../db/db_connect.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/user_login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user details
$query_user = "SELECT name, email, created_at FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Fetch ACTIVE complaints (excluding withdrawn)
// Fetch ACTIVE complaints (excluding withdrawn)
$query_complaints = "SELECT c.complaint_id, d.department_name, c.description, c.status 
                     FROM complaints c
                     LEFT JOIN departments d ON c.department_id = d.department_id
                     WHERE c.user_id = ? AND c.status != 'Withdrawn' 
                     ORDER BY c.created_at DESC";

$stmt_complaints = $conn->prepare($query_complaints);
$stmt_complaints->bind_param("i", $user_id);
$stmt_complaints->execute();
$result_complaints = $stmt_complaints->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradient background */
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.7); /* Transparent navbar */
            padding: 15px;
            text-align: center;
            color: white;
        }

        .navbar a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            transition: color 0.3s; /* Transition effect */
        }

        .navbar a:hover {
            color: #ffd700; /* Gold color on hover */
        }

        .dashboard-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s; /* Fade-in animation */
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s; /* Transition effects */
        }

        .btn:hover {
            background: #0056b3;
            transform: scale(1.05); /* Scale on hover */
        }

        .logout {
            background: #e74c3c;
        }

        .user-card {
            display: flex;
            align-items: center;
            background: #ADD8E6;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Add style for user icon */
        .user-card img {
            width: 50px;  /* Reduced size */
            height: 50px; /* Maintain aspect ratio */
            margin-right: 15px; /* Add some spacing between image and text */
        }

        .complaint-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s; /* Card hover effect */
        }

        .complaint-box:hover {
            transform: translateY(-5px); /* Lift the card on hover */
        }

        .complaint-actions { 
            display: flex; 
            gap: 10px; 
            margin-top: 10px; 
        }

        .track { background: #28a745; }
        .withdraw { background: #e74c3c; }
        .feedback { background: #f1c40f; color: black; }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Complaint Portal</h1>
    <a href="../auth/logout.php" class="btn logout">Logout</a>
</div>

<div class="dashboard-container">
    <!-- User Info -->
    <div class="user-card">
        <img src="../assets/user.png" alt="User Logo">
        <div>
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Joined on: <?php echo date("d M Y", strtotime($user['created_at'])); ?></p>
        </div>
    </div>

    <div class="header">
        <h2>Your Complaints</h2>
        <div>
            <a href="register_complaint.php" class="btn">+ Register Complaint</a>
            <a href="withdrawn_complaints.php" class="btn" style="background: #f39c12;">View Withdrawn Complaints</a>
        </div>
    </div>

    <div class="complaint-container">
        <?php while ($complaint = $result_complaints->fetch_assoc()): ?>
            <div class="complaint-box" data-complaint-id="<?php echo $complaint['complaint_id']; ?>">
                <div><strong>ID:</strong> <?php echo $complaint["complaint_id"]; ?></div>
                <div><strong>Department:</strong> <?php echo htmlspecialchars($complaint["department_name"] ?? 'N/A'); ?></div>
                <div><strong>Description:</strong> <?php echo htmlspecialchars($complaint["description"]); ?></div>
                <div><strong>Status:</strong> <?php echo htmlspecialchars($complaint["status"]); ?></div>
                <div class="complaint-actions">
                    <a href="track_complaint.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn track">Track</a>
                    <?php if ($complaint["status"] === "Pending"): ?>
                        <button class="btn withdraw" onclick="withdrawComplaint(<?php echo $complaint['complaint_id']; ?>)">Withdraw</button>
                    <?php endif; ?>
                    <?php if ($complaint["status"] === "Resolved"): ?>
                        <a href="feedback.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn feedback">Give Feedback</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<div id="loadingSpinner" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%);">
    <div class="spinner"></div>
</div>

<style>
.spinner {
  border: 10px solid #f3f3f3; /* Light grey */
  border-top: 10px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<script>
function withdrawComplaint(complaintId) {
    if (confirm("Are you sure you want to withdraw this complaint?")) {
        document.getElementById("loadingSpinner").style.display = "block"; // Show spinner
        fetch("withdraw_complaint.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + complaintId
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("loadingSpinner").style.display = "none"; // Hide spinner
            if (data.success) {
                // Find the complaint box
                const complaintBox = document.querySelector(`[data-complaint-id="${complaintId}"]`);
                // Find the status div by looking for the div containing "Status:"
                const statusDiv = Array.from(complaintBox.getElementsByTagName('div'))
                    .find(div => div.textContent.includes('Status:'));
                
                if (statusDiv) {
                    statusDiv.innerHTML = '<strong>Status:</strong> Withdrawn';
                }
                
                // Remove the withdraw button
                const withdrawButton = complaintBox.querySelector('.withdraw');
                if (withdrawButton) {
                    withdrawButton.remove();
                }
                
                alert("Complaint withdrawn successfully.");
            } else {
                alert("Error withdrawing complaint.");
            }
        })
        .catch(error => {
            document.getElementById("loadingSpinner").style.display = "none"; // Hide spinner
            console.error("Error:", error);
        });
    }
}
</script>
</body>
</html>

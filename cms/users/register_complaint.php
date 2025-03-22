<?php
session_start();
include "../db/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/user_login.php");
    exit();
}

$success = ""; // Success message
$error = "";   // Error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    
    $department_id = isset($_POST["department_id"]) ? $_POST["department_id"] : null;
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';

    if (!empty($department_id) && !empty($description)) {
        $query = "INSERT INTO complaints (user_id, department_id, description, status, created_at) 
                  VALUES (?, ?, ?, 'Pending', NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $user_id, $department_id, $description);

        if ($stmt->execute()) {
            $success = "Complaint submitted successfully!";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'user_dashboard.php';
                }, 3000);
            </script>";
        } else {
            $error = "Error registering complaint. Please try again.";
        }
    } else {
        $error = "All fields are required!";
    }
}

// Fetch departments for dropdown
$query_departments = "SELECT department_id, department_name FROM departments";
$result_departments = $conn->query($query_departments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Complaint</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register Complaint</h2>

    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label for="department_id">Select Department</label>
            <select name="department_id" required>
                <option value="">-- Select Department --</option>
                <?php while ($row = $result_departments->fetch_assoc()): ?>
                    <option value="<?php echo $row['department_id']; ?>">
                        <?php echo htmlspecialchars($row['department_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="input-group">
            <label for="description">Complaint Description</label>
            <textarea name="description" rows="4" required></textarea>
        </div>

        <button type="submit">Submit Complaint</button>
    </form>
</div>

</body>
</html>

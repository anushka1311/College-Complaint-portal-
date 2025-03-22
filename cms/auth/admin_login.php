<?php
session_start();
include "../db/db_connect.php"; // Database connection

// **Check if Admin Exists**
$query = "SELECT COUNT(*) FROM admins";
$result = $conn->query($query);
$row = $result->fetch_array();
$admin_count = $row[0];

if ($admin_count == 0) { // **Insert Admin Only If Not Exists**
    $hashed_password = password_hash("sm123", PASSWORD_BCRYPT);
    $insert_sql = "INSERT INTO admins (email, password) VALUES ('smjoshi@gmail.com', '$hashed_password')";
    $conn->query($insert_sql);
}

// **Handle Login**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $query = "SELECT admin_id, password FROM admins WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($admin_id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["admin_id"] = $admin_id;
            header("Location: ../admin/admin_dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "No account found with this email!";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        h1 { 
            color: #ffcc00; 
            margin-bottom: 20px; 
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            outline: none;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        input::placeholder {
            color: #ccc;
        }
        button {
            width: 100%;
            background: #ffcc00;
            color: #000;
            padding: 12px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        button:hover {
            background: #e6b800;
            transform: translateY(-2px);
        }
        .error { 
            color: red; 
            margin: 10px 0; 
        }
        .back-link {
            margin-top: 20px;
        }
        .back-link a {
            color: #ffcc00;
            text-decoration: none;
        }
        .back-link a:hover {
            color: #e6b800;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Login</h1>
        <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="back-link">
            <a href="../index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>

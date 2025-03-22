<?php
session_start();
include "../db/db_connect.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Fetch official details from database
    $query = "SELECT official_id, name, password, role FROM officials WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($official_id, $name, $db_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        // Directly compare passwords (since they are NOT hashed)
        if ($password === $db_password) {
            // Store session data
            $_SESSION["official_id"] = $official_id;
            $_SESSION["official_name"] = $name;
            $_SESSION["official_role"] = $role; 

            header("Location: ../officials/official_dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "No account found with this email!";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a1b9a, #ff4081);
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
            max-width: 400px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        h1 { 
            color: #ffeb3b; 
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
            background: #ffeb3b;
            color: #000;
            padding: 12px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        button:hover {
            background: #ffe57f;
            transform: translateY(-2px);
        }
        .error { 
            color: red; 
            margin: 10px 0; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Official Login</h1>
        <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Official Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 20px;">
            <a href="../index.php" style="color: #ffeb3b; text-decoration: none; padding: 8px 15px; border: 1px solid #ffeb3b; border-radius: 5px;">Back to Home</a>
        </p>
    </div>
</body>
</html>

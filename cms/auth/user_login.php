<?php
session_start();
include "../db/db_connect.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Fetch user from database
    $query = "SELECT user_id, name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["user_name"] = $name;
            echo "<script>alert('Login successful!'); window.location.href='../users/user_dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='user_login.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with this email!'); window.location.href='user_login.php';</script>";
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
    <title>User Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.05);
            filter: blur(20px);
            z-index: 0;
        }
        h4 { 
            color: #ffcc00; 
            margin-bottom: 20px; 
            position: relative; 
            z-index: 1; 
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            outline: none;
            font-size: 16px;
            position: relative;
            z-index: 1;
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
            position: relative;
            z-index: 1;
            transition: background 0.3s, transform 0.3s;
        }
        button:hover {
            background: #e6b800;
            transform: translateY(-2px);
        }
        .error { 
            color: red; 
            margin: 10px 0; 
            z-index: 1; 
        }
        @media (max-width: 600px) {
            h4 { font-size: 24px; }
            input, button { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h4>User Login</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
        <p class="mt-3 text-center">Don't have an account? 
            <a href="register.php" 
               style="color: #ffcc00; text-decoration: underline; cursor: pointer; position: relative; z-index: 2;">
                Register here
            </a>
        </p>
    </div>
</body>
</html>
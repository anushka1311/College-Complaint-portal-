
<?php
session_start();
include "db/db_connect.php"; // Database connection

// Check if user/admin/official is logged in and redirect accordingly
if (isset($_SESSION['user_id'])) {
    header("Location: users/user_dashboard.php");
    exit();
} elseif (isset($_SESSION['official_id'])) {
    header("Location: officials/official_dashboard.php");
    exit();
} elseif (isset($_SESSION['admin_id'])) {
    header("Location: admin/admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Complaint Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body { background: #f4f4f4; font-family: 'Roboto', sans-serif; }
        .navbar { background: #2c3e50; }
        .navbar-brand, .navbar a { color: #fff !important; }
        .btn-custom { background: #e67e22; color: #fff; border: none; transition: background 0.3s, transform 0.3s; }
        .btn-custom:hover { background: #d35400; transform: scale(1.05); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .hero-section {
            background: linear-gradient(135deg, #2196f3, #673ab7);
            height: 60vh;
            display: flex; 
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            font-size: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .black-text {color: black !important;}
        .hero-section h1, .hero-section p { position: relative; z-index: 1; }
        .section { padding: 50px 0; text-align: center; opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
        .section.visible { opacity: 1; transform: translateY(0); }
        .section h2 { color: #00796b; }
        .features, .testimonials { background: #fff; }
        .contact { background: #d35400; color: white; }
        .contact input, .contact textarea { width: 100%; padding: 10px; margin: 10px 0; border: none; }
        .contact button { background: #2c3e50; color: white; padding: 10px 20px; border: none; }
        footer { background: #2c3e50; color: white; padding: 10px; text-align: center; margin-top: 30px; }
        .card {margin: 10px 0;transition: transform 0.3s, box-shadow 0.3s;}
        .card:hover {transform: scale(1.05);box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);}
        .custom-card {width: 100%; /* Adjust the width as needed */margin: 0 auto; /* Center the card */}
        .arrow {font-size: 2rem; /* Adjust size as needed */line-height: 100px; /* Center the arrow vertically */}
        .card:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .btn-light, .btn-dark { margin: 5px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">Complaint Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    <a href="auth/user_login.php" class="btn btn-custom">User  Login</a>
                    <a href="auth/official_login.php" class="btn btn-light" style="color: black !important;">Official Login</a>
                    <a href="auth/admin_login.php" class="btn btn-dark">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div>
            <h1>Welcome to the College Complaint Portal</h1>
            <p>Submit and track complaints easily!</p>
            <a href="auth/register.php" class="btn btn-lg btn-custom">Get Started</a>

        </div>
    </section>

    <!-- About Section -->
    <section class="section features">
        <div class="container">
            <h2>Why Use Our Portal?</h2>
            <p>Our platform provides a seamless way for students, faculty, and staff to report and track complaints effectively.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3>🚀 Easy to Use</h3>
                            <p>Submit complaints in just a few clicks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3>📈 Real-time Tracking</h3>
                            <p>Monitor complaint status and receive updates.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3>🔒 Secure & Reliable</h3>
                            <p>All your data is safely stored and managed.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section">
        <div class="container">
            <h2>How It Works?</h2>
            <br>
            <div class="row text-center align-items-center">
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="card text-center custom-card">
                        <div class="card-body">
                            <h4><b>📝 Step 1:</b></h4><h5> Register</h4>
                            <p>Create your account using your student details.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 d-none d-md-block text-center">
                    <div class="arrow">➡️</div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="card text-center custom-card">
                        <div class="card-body">
                            <h4><B>📨 Step 2:</B></h4><h5> Submit Complaint</h4>
                            <p>Describe your issue and submit it.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 d-none d-md-block text-center">
                    <div class="arrow">➡️</div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="card text-center custom-card">
                        <div class="card-body">
                            <h4><b>👨‍💻 Step 3:</b></h4><h5>Admin Review</h5>
                            <p>Admins assign the complaint to the concerned official.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 d-none d-md-block text-center">
                    <div class="arrow">➡️</div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <div class="card text-center custom-card">
                        <div class="card-body">
                            <h4><b></b>✅ Step 4:</b></h4><h5>Resolution & Feedback</h5>
                            <p>Receive updates and provide feedback.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section testimonials">
        <div class="container">
            <h2>What Users Say</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p>"This portal made it so easy to report issues! I got a resolution within days." - <b>Rahul, Student</b></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p>"As an official, I can now efficiently manage and resolve complaints faster." - <b>Dr. Mehta, Faculty</b></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p>"Best system for keeping track of campus issues and ensuring smooth resolutions." - <b>Aditi, Admin</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact">
        <div class="container">
            <h2>Contact Us</h2>
            <p>If you have any queries, feel free to reach out.</p>
            <form action="send_contact.php" method="post">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Your Message" rows=" 4" required></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Complaint Management System | College Name</p>
        <div>
            <a href="#" class="text-white">Facebook</a> |
            <a href="#" class="text-white">Twitter</a> |
            <a href="#" class="text-white">Instagram</a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Fade-in effect for sections
        const sections = document.querySelectorAll('.section');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        });

        sections.forEach(section => {
            observer.observe(section);
        });
    </script>
</body>
</html>

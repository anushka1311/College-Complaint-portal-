<?php
session_start();
session_unset();  // Remove all session variables
session_destroy(); // Destroy the session

echo "<script>alert('You have been logged out.'); window.location.href='../index.php';</script>";
exit();
?>
n
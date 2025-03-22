<?php
session_start();
include "../db/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $complaint_id = $_POST["id"];
    $user_id = $_SESSION["user_id"]; // Ensure the user can only withdraw their own complaint

    $query = "UPDATE complaints SET status = 'Withdrawn' WHERE complaint_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $complaint_id, $user_id);
// ... existing code ...
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }

    $stmt->close();
    $conn->close();
}
?>

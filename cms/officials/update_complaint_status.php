<?php
session_start();
include "../db/db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$complaint_id = $data["complaint_id"];
$status = $data["status"];

$query = "UPDATE complaints SET official_status = ? WHERE complaint_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $complaint_id);
$success = $stmt->execute();

echo json_encode(["success" => $success]);
$stmt->close();
$conn->close();
?>

<?php
include 'includes/db.php';
session_start();

$review_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();

header("Location: dashboard.php");
exit();

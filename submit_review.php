<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $comment);
    $stmt->execute();

    header("Location: review.php?book_id=$book_id");
    exit();
}
?>

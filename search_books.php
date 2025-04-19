<?php
include 'includes/db.php';

$q = $_GET['q'] ?? '';
$q = "%$q%";

$stmt = $conn->prepare("SELECT id, title FROM books WHERE title LIKE ? LIMIT 10");
$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

header('Content-Type: application/json');
echo json_encode($books);
?>

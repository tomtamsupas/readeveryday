<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'includes/header.php';
?>

<h2>ยินดีต้อนรับ, <?php echo $_SESSION['user']['username']; ?>!</h2>
<p><a href="review.php">เขียนรีวิวหนังสือ</a> | <a href="logout.php">ออกจากระบบ</a></p>

<?php include 'includes/footer.php'; ?>

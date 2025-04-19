<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>BookReview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS และฟอนต์ -->
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="container header-container">
        <div class="logo">📘 Read Every Day</div>

        <nav class="main-nav">
            <ul class="menu">
                <li><a href="index.php">หน้าแรก</a></li>
                <li><a href="book.php">รายการหนังสือ</a></li>
                <li><a href="add_book.php">เพิ่มหนังสือ</a></li>
                <li><a href="review.php">รีวิว</a></li>
                <li><a href="survey.php">ให้คะแนนเว็บไซต์</a></li>
                <li><a href="about.php">ผู้จัดทำ</a></li>
            </ul>
        </nav>

        <div class="user-icons">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="logout.php">👤 ออกจากระบบ</a>
            <?php else: ?>
                <a href="login.php">👤 เข้าสู่ระบบ</a>
                <a href="register.php">📝 สมัครสมาชิก</a>
            <?php endif; ?>
        </div>

        <div class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</div>
    </div>
</header>

<?php
include 'includes/db.php';
include 'includes/header.php';

$rating_submitted = false;

// บันทึกผลเมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? null;
    $opinion = $_POST['opinion'] ?? '';

    if ($rating !== null && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO surveys (rating, opinion) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $rating, $opinion);
            $stmt->execute();
            $rating_submitted = true;
        }
    }
}

// คำนวณคะแนนเฉลี่ย
$result = $conn->query("SELECT AVG(rating) AS avg_rating FROM surveys WHERE rating IS NOT NULL");
$avg_rating = 0;

if ($result && $row = $result->fetch_assoc()) {
    $avg_rating = round($row['avg_rating'], 2);
}
?>

<div class="container" style="text-align:center; margin-top:30px;">
    <h2>⭐ คะแนนเฉลี่ยความพึงพอใจของเว็บไซต์</h2>
    <h1 style="font-size: 48px; color: #ff9800;"><?= $avg_rating ?> / 5</h1>

    <?php if ($rating_submitted): ?>
        <p style="color:green;">✅ ขอบคุณสำหรับความคิดเห็นของคุณ</p>
    <?php else: ?>
        <button onclick="document.getElementById('survey-form').style.display='block'; this.style.display='none';" style="margin-top:20px; padding:10px 20px; font-size:16px;">
            📋 ทำแบบสอบถาม
        </button>
    <?php endif; ?>

    <div id="survey-form" style="margin-top: 30px; display: <?= $rating_submitted ? 'none' : 'none' ?>;">
        <form method="post">
            <label for="rating">ให้คะแนนเว็บไซต์นี้:</label><br><br>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label>
                    <input type="radio" name="rating" value="<?= $i ?>" required>
                    <?= $i ?> ⭐
                </label>
            <?php endfor; ?>
            <br><br>

            <label for="opinion">ความคิดเห็นเพิ่มเติม:</label><br>
            <textarea name="opinion" rows="4" cols="50" placeholder="แสดงความคิดเห็นเพิ่มเติม..."></textarea><br><br>

            <button type="submit">ส่งแบบสอบถาม</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

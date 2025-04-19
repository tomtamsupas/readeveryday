<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h1>📚 ยินดีต้อนรับสู่ Read Every Day เว็บไซต์รีวิวหนังสือ </h1>
    <p>แบ่งปันประสบการณ์ และแชร์การอ่านหนังสือที่ชอบ ไม่ว่าจะเป็นนิยาย การพัฒนาคนเอง หรือการเรียนรู้ 😀</p>
</div>

<h2 style="text-align: center; margin-top: 30px;">📘 รวมรีวิวหนังสือ</h2>   
<div class="book-list" style="display: flex; flex-direction: column; gap: 20px; max-width: 800px; margin: 0 auto;">

<?php
$sql = "SELECT * FROM books";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($book = $result->fetch_assoc()) {
        // ดึงคะแนนเฉลี่ย
        $book_id = $book['id'];
        $rating_sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE book_id = $book_id";
        $rating_result = $conn->query($rating_sql);
        $rating = $rating_result->fetch_assoc()['avg_rating'];

        // ดึงคำรีวิวทั้งหมด
        $review_sql = "SELECT * FROM reviews WHERE book_id = $book_id ORDER BY created_at DESC";
        $reviews_result = $conn->query($review_sql);

        echo '<div class="book-card" style="display: flex; align-items: flex-start; gap: 20px; border:1px solid #ddd; padding:15px; border-radius:10px; background-color: #fff;">';

        // รูปปก
        echo '<img src="images/' . htmlspecialchars($book["cover_image"]) . '" alt="ปกหนังสือ" style="width: 120px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';

        // ข้อมูลหนังสือ
        echo '<div style="flex:1;">';
        echo '<h3 style="margin: 0 0 5px;">' . htmlspecialchars($book["title"]) . '</h3>';
        echo '<p style="margin: 0;">ผู้แต่ง: ' . htmlspecialchars($book["author"]) . '</p>';

        // คะแนนเฉลี่ย
        if (!is_null($rating)) {
            $rounded = round($rating, 1);
            echo '<p style="margin: 5px 0;">⭐ คะแนนเฉลี่ย: ' . $rounded . ' / 5</p>';
        } else {
            echo '<p style="margin: 5px 0;">⭐ ยังไม่มีคะแนน</p>';
        }

        // แสดงคำรีวิวล่าสุด (1 บรรทัด)
        if ($reviews_result->num_rows > 0) {
            $latest = $reviews_result->fetch_assoc();
            echo '<p style="font-style: italic; font-size: 0.9em; color: #555;">"' . htmlspecialchars($latest['comment']) . '"</p>';
        } else {
            echo '<p style="color: gray;">ยังไม่มีรีวิว</p>';
        }

        // ปุ่มแสดงรีวิวทั้งหมด
        echo '<button onclick="toggleReviews(\'reviews-' . $book_id . '\')" style="margin-top: 10px; background: none; border: none; color: #007BFF; cursor: pointer;">📖 อ่านรีวิวทั้งหมด</button>';

        // รายการรีวิวทั้งหมด (ซ่อนอยู่ก่อน)
        echo '<div id="reviews-' . $book_id . '" style="display:none; margin-top: 10px;">';
        if ($reviews_result->num_rows > 0) {
            // ต้องดึงข้อมูลอีกครั้ง เพราะ fetch_assoc() ไปแล้ว
            $reviews_result2 = $conn->query($review_sql);
            while ($review = $reviews_result2->fetch_assoc()) {
                echo '<div style="border-top: 1px solid #eee; padding-top: 5px; margin-top: 5px;">';
                echo '<p style="margin: 0;"><strong>ให้คะแนน:</strong> ' . htmlspecialchars($review["rating"]) . '/5</p>';
                echo '<p style="margin: 0;"><strong>รีวิว:</strong> ' . htmlspecialchars($review["comment"]) . '</p>';
                echo '</div>';
            }
        }
        echo '</div>'; // ปิด div รีวิวทั้งหมด

        echo '</div>'; // ปิดข้อมูลหนังสือ
        echo '</div>'; // ปิด book-card
    }
} else {
    echo "<p>ยังไม่มีหนังสือในระบบ</p>";
}
?>

</div>

<script>
function toggleReviews(id) {
    const section = document.getElementById(id);
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php include 'includes/footer.php'; ?>

<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$book_id = $_GET['book_id'] ?? 0;
?>

<?php include 'includes/header.php'; ?>

<h2>ค้นหาหนังสือเพื่อรีวิว</h2>
<input type="text" id="bookSearch" placeholder="พิมพ์ชื่อหนังสือ..." autocomplete="off" style="width: 100%; padding: 10px; font-size: 16px;">
<div id="suggestions" style="border: 1px solid #ccc; max-height: 200px; overflow-y: auto; display: none; position: absolute; background: white; z-index: 9999; width: 100%; box-shadow: 0 2px 6px rgba(0,0,0,0.2);"></div>

<br><br>

<?php
if ($book_id) {
    // โหลดชื่อหนังสือ
    $book_stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
    $book_stmt->bind_param("i", $book_id);
    $book_stmt->execute();
    $book_result = $book_stmt->get_result();
    $book = $book_result->fetch_assoc();

    echo "<h3>หนังสือที่เลือก: <em>" . htmlspecialchars($book['title']) . "</em></h3>";

    // ถ้ามี POST เขียนรีวิว
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];
        $user_id = $_SESSION['user']['id'];

        $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $book_id, $rating, $comment);
        $stmt->execute();

        echo "<p style='color:green;'>บันทึกรีวิวเรียบร้อยแล้ว</p>";
    }

    // ฟอร์มรีวิว
    ?>
    <form method="post">
        <label>ให้คะแนน (1-5):</label><br>
        <select name="rating" required>
            <?php for ($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select><br><br>

        <label>ความคิดเห็น:</label><br>
        <textarea name="comment" rows="5" cols="40" required></textarea><br><br>

        <button type="submit">ส่งรีวิว</button>
    </form>

    <hr>
    <h3>รีวิวล่าสุด</h3>
    <?php
    $review_stmt = $conn->prepare("
        SELECT r.*, u.username 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.book_id = ? 
        ORDER BY r.id DESC
    ");
    $review_stmt->bind_param("i", $book_id);
    $review_stmt->execute();
    $reviews = $review_stmt->get_result();

    if ($reviews->num_rows > 0):
        while ($review = $reviews->fetch_assoc()):
    ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <strong><?= htmlspecialchars($review['username']) ?></strong> ให้คะแนน <?= $review['rating'] ?>/5<br>
            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
        </div>
    <?php endwhile; else: ?>
        <p>ยังไม่มีรีวิวสำหรับหนังสือเล่มนี้</p>
    <?php endif; ?>

<?php } else { ?>
    <p>กรุณาพิมพ์และเลือกหนังสือก่อนเขียนรีวิว</p>
<?php } ?>

<?php include 'includes/footer.php'; ?>

<script>
document.getElementById('bookSearch').addEventListener('input', function () {
    const query = this.value.trim();
    const suggestions = document.getElementById('suggestions');

    if (query.length === 0) {
        suggestions.style.display = 'none';
        suggestions.innerHTML = '';
        return;
    }

    fetch('search_books.php?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            suggestions.innerHTML = '';
            if (data.length > 0) {
                data.forEach(book => {
                    const div = document.createElement('div');
                    div.textContent = book.title;
                    div.style.padding = '8px';
                    div.style.cursor = 'pointer';
                    div.onclick = () => {
                        window.location.href = 'review.php?book_id=' + book.id;
                    };
                    suggestions.appendChild(div);
                });
                suggestions.style.display = 'block';
            } else {
                suggestions.innerHTML = '<div style="padding:8px;">ไม่พบหนังสือ</div>';
                suggestions.style.display = 'block';
            }
        });
});
</script>

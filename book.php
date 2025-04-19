<?php include 'includes/db.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container py-5 text-center">
    <h2>🔍 ค้นหาหนังสือ</h2>
    <form method="GET" class="mb-4">
        <input type="text" name="q" placeholder="พิมพ์ชื่อหนังสือ..." class="form-control w-50 mx-auto" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit" class="btn btn-primary mt-3">ค้นหา</button>
    </form>

    <?php
    if (isset($_GET['q']) && trim($_GET['q']) !== '') {
        $search = $conn->real_escape_string($_GET['q']);

        $sql = "
            SELECT 
                b.*, 
                (SELECT comment FROM reviews WHERE book_id = b.id ORDER BY id DESC LIMIT 1) AS latest_comment
            FROM books b
            WHERE b.title LIKE '%$search%'
        ";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()) {
                ?>
                <div class="card mx-auto mb-5 text-center" style="max-width: 600px; border: none; margin-top: 30px;">
                <img src="images/<?= htmlspecialchars($book['cover_image']) ?>" 
                class="card-img-top mx-auto" 
                style="width: 220px; height: auto;" 
                alt="<?= htmlspecialchars($book['title']) ?>">


                    <div class="card-body">
                        <h3 class="card-title mt-3" style="font-weight: bold; font-size: 1.8rem;">
                            <?= htmlspecialchars($book['title']) ?>
                        </h3>

                        <p><strong>ผู้แต่ง:</strong> <?= htmlspecialchars($book['author']) ?></p>

                        <div class="mt-3">
                            <h5><strong>คำอธิบาย:</strong></h5>
                            <p style="text-align: left;"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                        </div>

                        <?php if (!empty($book['latest_comment'])): ?>
                            <div class="mt-3">
                                <i class="bi bi-chat-left-quote"></i> <strong>รีวิวล่าสุด:</strong>
                                <p><em>"<?= htmlspecialchars($book['latest_comment']) ?>"</em></p>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">ยังไม่มีรีวิว</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-muted">ไม่พบหนังสือที่ค้นหา</p>';
        }
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>

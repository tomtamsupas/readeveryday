<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// ✅ ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user'])) {
    echo "<p style='color:red; text-align: center;'>⛔ กรุณาเข้าสู่ระบบก่อนใช้งานหน้านี้</p>";
    include 'includes/footer.php';
    exit;
}

// ✅ ตรวจสอบสิทธิ์ (หากต้องการจำกัดให้เฉพาะ admin ให้เปลี่ยนเป็น false ข้างล่าง)
$can_add = true;
// ตัวอย่างถ้าต้องการเฉพาะแอดมินเท่านั้น:
// $can_add = ($_SESSION['user']['role'] === 'admin');

// ✅ การจัดการการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST" && $can_add) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $cover_image = '';

    // ✅ อัปโหลดภาพ
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "images/";
        $cover_image = basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $cover_image;

        // ตรวจสอบและสร้างโฟลเดอร์หากยังไม่มี
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file);
    }

    // ✅ บันทึกลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO books (title, author, description, cover_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $author, $description, $cover_image);
    $stmt->execute();

    echo "<p style='color:green; text-align:center;'>✅ เพิ่มหนังสือเรียบร้อยแล้ว</p>";
}
?>

<!-- ✅ ฟอร์มเพิ่มหนังสือ -->
<?php if ($can_add): ?>
    <div class="container" style="max-width: 600px; margin: 0 auto; padding: 30px; text-align: center;">
        <h2>📚 เพิ่มหนังสือใหม่</h2>
        <button onclick="document.getElementById('add-form').style.display='block'; this.style.display='none';"
                style="padding: 10px 20px; margin-bottom: 20px;">➕ เพิ่มหนังสือ</button>

        <div id="add-form" style="display: none;">
            <form method="post" enctype="multipart/form-data">
                <div style="text-align: left;">
                    <label>ชื่อหนังสือ:</label><br>
                    <input type="text" name="title" required style="width:100%; padding:8px;"><br><br>

                    <label>ผู้แต่ง:</label><br>
                    <input type="text" name="author" required style="width:100%; padding:8px;"><br><br>

                    <label>คำอธิบาย:</label><br>
                    <textarea name="description" rows="5" required style="width:100%; padding:8px;"></textarea><br><br>

                    <label>รูปปก:</label><br>
                    <input type="file" name="cover_image" accept="image/*"><br><br>
                </div>

                <button type="submit" style="padding: 10px 20px;">💾 บันทึก</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <p style="color:red; text-align:center;">⛔ คุณไม่มีสิทธิ์เพิ่มหนังสือ</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

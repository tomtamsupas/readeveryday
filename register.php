<?php
session_start();
include 'includes/db.php';

$error = "";

// กด submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password_raw = $_POST['password'];
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    // ตรวจสอบว่ามี username ซ้ำไหม
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "ชื่อผู้ใช้นี้ถูกใช้แล้ว กรุณาเลือกชื่อใหม่";
        $_SESSION['old_username'] = $username;
        header("Location: register.php");
        exit();
    }

    // สมัครใหม่
    $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insert->bind_param("ss", $username, $password);

    if ($insert->execute()) {
        // สมัครเสร็จแล้ว redirect ไป login แบบปลอดภัย
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['register_error'] = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
        $_SESSION['old_username'] = $username;
        header("Location: register.php");
        exit();
    }
}

// เตรียมค่าไว้แสดงในฟอร์ม
$error = $_SESSION['register_error'] ?? "";
$old_username = $_SESSION['old_username'] ?? "";

// clear session error หลังแสดง
unset($_SESSION['register_error'], $_SESSION['old_username']);
?>

<?php include 'includes/header.php'; ?>

<h2>สมัครสมาชิก</h2>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post" autocomplete="off">
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required autocomplete="off"><br>
    <input type="password" name="password" placeholder="รหัสผ่าน" required autocomplete="new-password"><br>
    <button type="submit">สมัครสมาชิก</button>
</form>

<p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>

<?php include 'includes/footer.php'; ?>

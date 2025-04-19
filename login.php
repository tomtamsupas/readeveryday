<?php
session_start();
include 'includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            header("Location: dashboard.php"); 
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบผู้ใช้";
    }
}
?>

<?php include 'includes/header.php'; ?>

<h2>เข้าสู่ระบบ</h2>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post" autocomplete="off">
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required autocomplete="off"><br>
    <input type="password" name="password" placeholder="รหัสผ่าน" required autocomplete="new-password"><br>
    <button type="submit">เข้าสู่ระบบ</button>
</form>

<p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>

<?php include 'includes/footer.php'; ?>

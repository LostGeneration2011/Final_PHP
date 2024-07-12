<?php
session_start();
include 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra điều kiện tên đăng nhập và mật khẩu
    if (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username)) {
        $error = "Tên đăng nhập phải chứa từ 5-20 ký tự và chỉ bao gồm chữ cái, số và dấu gạch dưới.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $error = "Mật khẩu phải chứa ít nhất 8 ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường, một số và một ký tự đặc biệt.";
    } else {
        // Sử dụng prepared statement để ngăn ngừa SQL injection
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                $success = "Đăng nhập thành công. Đang chuyển hướng...";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'products.php';
                        }, 2000); // Chờ 2 giây trước khi chuyển hướng
                      </script>";
            } else {
                $error = "Mật khẩu không đúng.";
            }
        } else {
            $error = "Không tìm thấy người dùng.";
        }
        $stmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>
<h2>Đăng nhập</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Mật khẩu:</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Đăng nhập</button>
</form>
<?php include 'includes/footer.php'; ?>

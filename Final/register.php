<?php
include 'includes/db.php';

function is_valid_username($username) {
    // Kiểm tra nếu tên đăng nhập chứa từ 5-20 ký tự và chỉ bao gồm chữ cái, số và dấu gạch dưới
    return preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username);
}

function is_valid_password($password) {
    // Kiểm tra nếu mật khẩu chứa ít nhất 8 ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường, một số và một ký tự đặc biệt
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (is_valid_username($username) && is_valid_password($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Kiểm tra tên đăng nhập đã tồn tại hay chưa
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Thêm người dùng mới vào cơ sở dữ liệu
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute() === TRUE) {
                $success = "Đăng ký thành công. Bạn có thể <a href='login.php'>đăng nhập</a> ngay bây giờ.";
            } else {
                $error = "Lỗi: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error = "Tên đăng nhập đã tồn tại.";
        }
        $stmt->close();
    } else {
        if (!is_valid_username($username)) {
            $error = "Tên đăng nhập phải chứa từ 5-20 ký tự và chỉ bao gồm chữ cái, số và dấu gạch dưới.";
        } elseif (!is_valid_password($password)) {
            $error = "Mật khẩu phải chứa ít nhất 8 ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường, một số và một ký tự đặc biệt.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<h2>Đăng ký</h2>
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
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Đăng ký</button>
</form>
<?php include 'includes/footer.php'; ?>

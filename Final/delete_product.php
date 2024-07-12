<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: products.php");
        exit();
    } else {
        $error = "Lỗi: " . $sql . "<br>" . $conn->error;
    }
} else {
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
}
?>

<?php include 'includes/header.php'; ?>
<h2>Xóa Sản phẩm</h2>
<p>Bạn có chắc chắn muốn xóa sản phẩm <strong><?php echo $product['name']; ?></strong>?</p>
<form method="POST" action="">
    <button type="submit" class="btn btn-danger">Xóa</button>
    <a href="products.php" class="btn btn-secondary">Hủy</a>
</form>
<?php include 'includes/footer.php'; ?>

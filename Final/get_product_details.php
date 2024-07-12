<?php
include 'includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ép kiểu thành số nguyên để tránh SQL injection
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $price = intval($product['price']); // Ép kiểu thành số nguyên
        $formatted_price = number_format($price, 0, ',', '.') . ' VND'; // Định dạng không có phần thập phân
        echo "<h5>" . htmlspecialchars($product['name']) . "</h5>";
        echo "<p>Giá: " . $formatted_price . "</p>";
        echo "<p>Danh mục: " . htmlspecialchars($product['category']) . "</p>";
        echo "<p>Số lượng: " . htmlspecialchars($product['quantity']) . "</p>"; // Hiển thị số lượng sản phẩm
        echo "<p>Mô tả: " . htmlspecialchars($product['description']) . "</p>";
        echo "<img src='" . htmlspecialchars($product['image']) . "' alt='" . htmlspecialchars($product['name']) . "' class='img-fluid'>";
    } else {
        echo "<p>Không tìm thấy sản phẩm.</p>";
    }
} else {
    echo "<p>ID sản phẩm không hợp lệ.</p>";
}
?>

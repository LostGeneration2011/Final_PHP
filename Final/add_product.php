<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);

    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Tên sản phẩm không được để trống.";
    }

    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Giá sản phẩm phải là một số dương.";
    }

    if (empty($category)) {
        $errors[] = "Danh mục không được để trống.";
    }

    if (!is_numeric($quantity) || $quantity < 0) {
        $errors[] = "Số lượng sản phẩm phải là một số không âm.";
    }

    // Check for duplicate product name
    $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Tên sản phẩm đã tồn tại.";
    }
    $stmt->close();

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category, quantity, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $name, $price, $category, $quantity, $image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target) && $stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Đã xảy ra lỗi khi thêm sản phẩm: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Thêm Sản phẩm Mới</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="add_product.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Tên Sản phẩm:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="price">Giá:</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="category">Danh mục:</label>
            <input type="text" class="form-control" id="category" name="category" required>
        </div>
        <div class="form-group">
            <label for="quantity">Số lượng:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="form-group">
            <label for="image">Hình ảnh:</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <button type="submit" class="btn btn-primary">Thêm Sản phẩm</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

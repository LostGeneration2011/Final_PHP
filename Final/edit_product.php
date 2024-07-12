<?php
include 'includes/db.php';

function checkProductExists($conn, $name, $id = null) {
    $sql = "SELECT id FROM products WHERE name = ?";
    if ($id) {
        $sql .= " AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $name, $id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $name);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $quantity = $_POST['quantity'];
        $image = $_FILES['image']['name'];
        $target = "images/".basename($image);

        $errors = [];

        // Validate inputs
        if (empty($name)) {
            $errors[] = "Tên sản phẩm không được để trống.";
        }

        if (checkProductExists($conn, $name, $id)) {
            $errors[] = "Tên sản phẩm đã tồn tại.";
        }

        if (empty($errors)) {
            if (!empty($image)) {
                // Move the uploaded image to the images directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $sql = "UPDATE products SET name=?, price=?, category=?, quantity=?, image=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sdsssi', $name, $price, $category, $quantity, $target, $id); // Use $target as the image path
                } else {
                    $errors[] = "Failed to upload image.";
                }
            } else {
                $sql = "UPDATE products SET name=?, price=?, category=?, quantity=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sdssi', $name, $price, $category, $quantity, $id);
            }

            if (empty($errors) && $stmt->execute()) {
                header("Location: index.php");
            } else {
                $errors[] = "Error: " . $stmt->error;
            }
        }
    }

    $sql = "SELECT * FROM products WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Chỉnh sửa Sản phẩm</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_product.php?id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Tên Sản phẩm:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Giá:</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Danh mục:</label>
            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
        </div>
        <div class="form-group">
            <label for="quantity">Số lượng:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Hình ảnh:</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật Sản phẩm</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

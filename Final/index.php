<?php
session_start();
include 'includes/db.php';
?>

<?php include 'includes/header.php'; ?>

<div class="jumbotron">
    <h1 class="display-4">Chào mừng đến với Cửa hàng Sản phẩm của chúng tôi</h1>
    <p class="lead">Khám phá các sản phẩm đa dạng của chúng tôi và tìm kiếm những gì phù hợp với bạn nhất.</p>
    <hr class="my-4">
    <p>Chúng tôi cung cấp các sản phẩm chất lượng cao với giá tốt nhất. Duyệt qua các danh mục và tìm kiếm các mặt hàng yêu thích của bạn.</p>
    <a class="btn btn-primary btn-lg" href="products.php" role="button">Khám phá Sản phẩm</a>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Cửa hàng Sản phẩm</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Trang chủ <span class="sr-only">(hiện tại)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="products.php">Sản phẩm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="about.php">Giới thiệu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contact.php">Liên hệ</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>Sản phẩm của chúng tôi</h2>
    <div class="row">
        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()): 
            $price = intval($row['price']); // Ép kiểu thành số nguyên
            $formatted_price = number_format($price, 0, ',', '.') . ' VND'; // Định dạng không có phần thập phân ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <p class="card-text"><strong>Số lượng:</strong> <span class="product-quantity"><?php echo htmlspecialchars($row['quantity']); ?></span></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary view-product" data-id="<?php echo $row['id']; ?>" data-toggle="modal" data-target="#productModal">Xem</button>
                                <button type="button" class="btn btn-sm btn-outline-primary buy-product" data-id="<?php echo $row['id']; ?>">Mua</button>
                            </div>
                            <small class="text-muted"><?php echo $formatted_price; ?></small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Chi tiết sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="productDetails">
                    <!-- Nội dung chi tiết sản phẩm sẽ được load tại đây -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Bao gồm tập tin JavaScript -->
<script src="js/scripts.js"></script>

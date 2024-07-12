<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

// Initialize variables
$filter = "";
$searchTerm = "";
$category = "";
$whereClauses = [];
$params = [];
$types = "";

// Handle GET requests
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $category = $_GET['category'];
        $whereClauses[] = "category = ?";
        $params[] = $category;
        $types .= "s";
    }
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = "%" . $_GET['search'] . "%";
        $whereClauses[] = "name LIKE ?";
        $params[] = $searchTerm;
        $types .= "s";
    }
}

// Construct filter
if (!empty($whereClauses)) {
    $filter = " WHERE " . implode(" AND ", $whereClauses);
}

// Pagination setup
$products_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// Get total number of products
$count_sql = "SELECT COUNT(*) as total FROM products" . $filter;
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// Get products for the current page
$sql = "SELECT * FROM products" . $filter . " LIMIT ?, ?";
$params[] = $offset;
$params[] = $products_per_page;
$types .= "ii";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get list of categories
$category_sql = "SELECT DISTINCT category FROM products";
$category_result = $conn->query($category_sql);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Danh sách Sản phẩm</h2>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Quay trở lại trang chủ</a>
    </div>
    <form method="GET" action="" class="form-inline mb-4">
        <div class="form-group mx-sm-3 mb-2">
            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars(isset($searchTerm) ? $searchTerm : ''); ?>">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="category">
                <option value="">Tất cả danh mục</option>
                <?php while($cat_row = $category_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($cat_row['category']); ?>" <?php if ($category == $cat_row['category']) echo 'selected'; ?>><?php echo htmlspecialchars($cat_row['category']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter"></i> Lọc</button>
        <a href="add_product.php" class="btn btn-success mb-2 ml-2"><i class="fas fa-plus"></i> Thêm Sản phẩm</a>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Danh mục</th>
                <th>Số lượng</th>
                <th>Hình ảnh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><img src="<?php echo strpos($row['image'], 'images/') === 0 ? htmlspecialchars($row['image']) : 'images/' . htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="50"></td>
                <td>
                    <a href="edit_product.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                    <a href="delete_product.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&category=<?php echo htmlspecialchars($category); ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">Previous</a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo htmlspecialchars($category); ?>&search=<?php echo htmlspecialchars($searchTerm); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&category=<?php echo htmlspecialchars($category); ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include 'includes/footer.php'; ?>

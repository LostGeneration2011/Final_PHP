<?php
include 'includes/db.php';

$response = array('success' => false, 'error' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = intval($_POST['id']);
    
    // Lấy số lượng hiện tại của sản phẩm
    $sql = "SELECT quantity FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $currentQuantity = intval($product['quantity']);
        
        if ($currentQuantity > 0) {
            // Giảm số lượng sản phẩm
            $newQuantity = $currentQuantity - 1;
            $sql = "UPDATE products SET quantity = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $newQuantity, $productId);
            
            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['error'] = "Không thể cập nhật số lượng sản phẩm.";
            }
        } else {
            $response['error'] = "Sản phẩm đã hết hàng.";
        }
    } else {
        $response['error'] = "Không tìm thấy sản phẩm.";
    }
    
    $stmt->close();
} else {
    $response['error'] = "Phương thức yêu cầu không hợp lệ.";
}

$conn->close();
echo json_encode($response);
?>

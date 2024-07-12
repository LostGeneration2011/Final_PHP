$(document).ready(function(){
    $('.view-product').on('click', function(){
        var productId = $(this).data('id');
        $.ajax({
            url: 'get_product_details.php',
            type: 'GET',
            data: { id: productId },
            success: function(response) {
                $('#productDetails').html(response);
            },
            error: function() {
                $('#productDetails').html('<p>Đã xảy ra lỗi khi tải chi tiết sản phẩm.</p>');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.buy-product').forEach(function(button) {
        button.addEventListener('click', function() {
            var productId = this.getAttribute('data-id');
            var productCard = this.closest('.card');
            var productQuantityElement = productCard.querySelector('.product-quantity');
            var currentQuantity = parseInt(productQuantityElement.textContent);

            if (currentQuantity > 0) {
                fetch('buy_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ id: productId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        productQuantityElement.textContent = currentQuantity - 1;
                        alert('Bạn đã mua sản phẩm thành công!');
                    } else {
                        alert('Lỗi: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi khi mua sản phẩm.');
                });
            } else {
                alert('Sản phẩm đã hết hàng.');
            }
        });
    });
});

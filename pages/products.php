<?php
/**
 * FashionHub - Danh sách sản phẩm
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../models/Product.php';

$pageTitle = 'Sản phẩm';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = Config::get('ITEMS_PER_PAGE', 12);
$offset = ($page - 1) * $limit;

$productModel = new Product();
$products = $productModel->getAll($limit, $offset);
$totalProducts = $productModel->getTotalCount();
$totalPages = ceil($totalProducts / $limit);

include __DIR__ . '/../includes/header.php';
?>

    <div class="page-header">
        <h1>Danh sách sản phẩm</h1>
        <p>Chọn sản phẩm yêu thích của bạn</p>
    </div>

    <div class="products-container">
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo !empty($product['image']) ? Config::get('APP_URL') . '/uploads/' . $product['image'] : 'https://placehold.co/250x250/cccccc/999999?text=Khong+co+anh'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             loading="lazy"
                             onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22250%22 height=%22250%22><rect fill=%22%23f0f0f0%22 width=%22250%22 height=%22250%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2214%22>Khong co anh</text></svg>'">
                    </div>
                    
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">
                            <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> <?php echo Config::get('CURRENCY_SYMBOL'); ?></span>
                        </div>

                        <div class="product-actions">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <?php if ($product['stock'] > 0): ?>
                                <button class="btn btn-sm btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm" disabled>Hết hàng</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Không có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1" class="btn-page">Đầu tiên</a>
            <a href="?page=<?php echo $page - 1; ?>" class="btn-page">Trước</a>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        for ($i = $start; $i <= $end; $i++):
        ?>
            <a href="?page=<?php echo $i; ?>" class="btn-page <?php echo $i === $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="btn-page">Tiếp</a>
            <a href="?page=<?php echo $totalPages; ?>" class="btn-page">Cuối cùng</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
function addToCart(productId, productName, price) {
    fetch('<?php echo Config::get('APP_URL'); ?>/api/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}
</script>

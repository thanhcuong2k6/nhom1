<?php
/**
 * Search Page - Trang tìm kiếm sản phẩm
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Product.php';

$pageTitle = 'Tìm kiếm sản phẩm';

// Lấy từ khóa tìm kiếm
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];
$totalResults = 0;

if (!empty($searchQuery)) {
    $db = new Database();
    $conn = $db->getConnection();

    // Tìm kiếm sản phẩm theo tên hoặc description
    $searchTerm = '%' . $searchQuery . '%';
    $query = "SELECT id, name, slug, description, price, image, stock 
              FROM products 
              WHERE (name LIKE ? OR description LIKE ?) AND active = 1
              LIMIT 50";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $totalResults = count($products);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="search-section">
    <div class="search-container">
        <div class="search-header">
            <h1>🔍 Tìm kiếm sản phẩm</h1>
            
            <form method="GET" class="search-form-main">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="Tìm kiếm sản phẩm..." 
                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                        autofocus
                    >
                    <button type="submit" class="search-btn">
                        🔍 Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($searchQuery)): ?>
            <div class="search-results-header">
                <p class="results-info">
                    Kết quả tìm kiếm cho "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                    <span class="results-count">(<?php echo $totalResults; ?> sản phẩm)</span>
                </p>
            </div>

            <?php if ($totalResults > 0): ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo !empty($product['image']) ? Config::get('APP_URL') . 'uploads/' . $product['image'] : 'https://placehold.co/250x250/cccccc/999999?text=No+Image'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 loading="lazy"
                                 onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22250%22 height=%22250%22><rect fill=%22%23f0f0f0%22 width=%22250%22 height=%22250%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2214%22>No Image</text></svg>'">
                            <?php if ($product['stock'] <= 0): ?>
                                <span class="product-badge out-of-stock">Hết hàng</span>
                            <?php else: ?>
                                <span class="product-badge">Còn <?php echo $product['stock']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description">
                                <?php echo substr(htmlspecialchars($product['description']), 0, 50); ?>...
                            </p>
                            
                            <div class="product-price">
                                <span class="price">
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> 
                                    <?php echo Config::get('CURRENCY_SYMBOL', 'đ'); ?>
                                </span>
                            </div>

                            <div class="product-actions">
                                <a href="<?php echo Config::get('APP_URL'); ?>pages/product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-view">
                                    👁️ Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">😕</div>
                    <h2>Không tìm thấy sản phẩm</h2>
                    <p>
                        Chúng tôi không tìm thấy sản phẩm nào khớp với "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>".
                    </p>
                    <p class="suggestions">
                        Hãy thử:
                        <br>
                        - Kiểm tra chính tả<br>
                        - Sử dụng từ khóa chung hơn<br>
                        - Duyệt danh mục sản phẩm
                    </p>
                    <a href="<?php echo Config::get('APP_URL'); ?>pages/products.php" class="btn btn-browse">
                        🛍️ Duyệt tất cả sản phẩm
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-search">
                <div class="no-search-icon">🔍</div>
                <h2>Nhập từ khóa để tìm kiếm</h2>
                <p>Hãy nhập tên sản phẩm hoặc từ khóa bạn muốn tìm</p>
                <a href="<?php echo Config::get('APP_URL'); ?>pages/products.php" class="btn btn-browse">
                    🛍️ Xem tất cả sản phẩm
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.search-section {
    padding: 40px 20px;
    background: #f8f9fa;
    min-height: calc(100vh - 250px);
}

.search-container {
    max-width: 1200px;
    margin: 0 auto;
}

.search-header {
    text-align: center;
    margin-bottom: 40px;
}

.search-header h1 {
    font-size: 32px;
    color: #333;
    margin: 0 0 30px;
}

.search-form-main {
    max-width: 500px;
    margin: 0 auto;
}

.search-input-group {
    display: flex;
    gap: 10px;
}

.search-input {
    flex: 1;
    padding: 14px 20px;
    border: 2px solid #ddd;
    border-radius: 6px 0 0 6px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-btn {
    padding: 14px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 0 6px 6px 0;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.search-results-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #ddd;
}

.results-info {
    font-size: 18px;
    color: #333;
    margin: 0;
}

.results-count {
    color: #667eea;
    font-weight: 600;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    overflow: hidden;
    background: #f0f0f0;
    aspect-ratio: 1;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #10b981;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.product-badge.out-of-stock {
    background: #ef4444;
}

.product-info {
    padding: 15px;
}

.product-info h3 {
    font-size: 14px;
    color: #333;
    margin: 0 0 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-description {
    font-size: 12px;
    color: #666;
    margin: 0 0 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    margin-bottom: 12px;
}

.price {
    font-size: 14px;
    font-weight: 700;
    color: #667eea;
}

.product-actions {
    display: flex;
    gap: 10px;
}

.btn {
    flex: 1;
    padding: 8px 12px;
    text-align: center;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-view {
    background: #667eea;
    color: white;
}

.btn-view:hover {
    background: #5568d3;
}

.no-results,
.no-search {
    background: white;
    padding: 60px 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.no-results-icon,
.no-search-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.no-results h2,
.no-search h2 {
    font-size: 24px;
    color: #333;
    margin: 0 0 15px;
}

.no-results p,
.no-search p {
    color: #666;
    font-size: 16px;
    margin: 0 0 10px;
}

.suggestions {
    font-size: 14px !important;
    line-height: 1.8;
}

.btn-browse {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 20px;
}

.btn-browse:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .search-section {
        padding: 20px 10px;
    }

    .search-header h1 {
        font-size: 24px;
    }

    .search-input-group {
        flex-direction: column;
    }

    .search-input,
    .search-btn {
        border-radius: 6px;
        width: 100%;
    }

    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }

    .no-results,
    .no-search {
        padding: 40px 15px;
    }

    .no-results h2,
    .no-search h2 {
        font-size: 20px;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>

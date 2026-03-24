<?php
/**
 * FashionHub - API - Xóa sản phẩm khỏi giỏ hàng
 */

session_start();
require_once __DIR__ . '/../models/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
        exit;
    }

    $cart = new Cart();
    $cart->removeItem($data['product_id']);
    echo json_encode(['success' => true, 'message' => 'Đã xóa sản phẩm']);
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?>

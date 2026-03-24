<?php
/**
 * FashionHub - API - Thêm sản phẩm vào giỏ hàng
 */

session_start();
require_once __DIR__ . '/../models/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
        exit;
    }

    $cart = new Cart();
    $cart->addItem(
        $data['product_id'],
        $data['quantity'],
        $data['price'] ?? 0,
        $data['product_name'] ?? 'Sản phẩm'
    );

    echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?>

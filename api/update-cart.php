<?php
/**
 * FashionHub - API - Cập nhật giỏ hàng
 */

session_start();
require_once __DIR__ . '/../models/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['product_id']) || !isset($data['change'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
        exit;
    }

    $cart = new Cart();
    $items = $cart->getItems();
    
    if (isset($items[$data['product_id']])) {
        $newQty = $items[$data['product_id']]['quantity'] + $data['change'];
        $cart->updateQuantity($data['product_id'], $newQty);
        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?>

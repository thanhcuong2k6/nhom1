<?php
/**
 * FashionHub - API - Xóa toàn bộ giỏ hàng
 */

session_start();
require_once __DIR__ . '/../models/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = new Cart();
    $cart->clear();
    echo json_encode(['success' => true, 'message' => 'Giỏ hàng đã được xóa']);
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?>

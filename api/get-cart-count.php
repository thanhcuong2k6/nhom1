<?php
/**
 * FashionHub - API - Lấy số lượng giỏ hàng
 */

session_start();
require_once __DIR__ . '/../models/Cart.php';

header('Content-Type: application/json');

$cart = new Cart();
echo json_encode(['count' => $cart->getItemCount()]);
?>

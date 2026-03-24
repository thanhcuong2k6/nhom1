<?php
/**
 * FashionHub - Mô hình Giỏ hàng
 */

class Cart {
    private $items = [];

    public function __construct() {
        if (isset($_SESSION['cart'])) {
            $this->items = $_SESSION['cart'];
        }
    }

    public function addItem($productId, $quantity = 1, $price = 0, $productName = '') {
        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity'] += $quantity;
        } else {
            $this->items[$productId] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'quantity' => $quantity,
                'price' => $price
            ];
        }
        $this->save();
    }

    public function removeItem($productId) {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);
            $this->save();
        }
    }

    public function updateQuantity($productId, $quantity) {
        if (isset($this->items[$productId])) {
            if ($quantity <= 0) {
                $this->removeItem($productId);
            } else {
                $this->items[$productId]['quantity'] = $quantity;
                $this->save();
            }
        }
    }

    public function getItems() {
        return $this->items;
    }

    public function getItemCount() {
        return count($this->items);
    }

    public function getTotalQuantity() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    public function getTotalPrice() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clear() {
        $this->items = [];
        $this->save();
    }

    private function save() {
        $_SESSION['cart'] = $this->items;
    }
}
?>

<?php
/**
 * FashionHub - Mô hình Sản phẩm
 */

require_once __DIR__ . '/../config/Database.php';

class Product {
    private $db;
    private $table = 'products';

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll($limit = 12, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE active = 1 LIMIT $limit OFFSET $offset";
        $result = $this->db->query($sql);
        
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function search($keyword) {
        $keyword = $this->db->escape($keyword);
        $sql = "SELECT * FROM {$this->table} 
                WHERE (name LIKE '%$keyword%' OR description LIKE '%$keyword%') 
                AND active = 1";
        $result = $this->db->query($sql);
        
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    public function getByCategoryId($categoryId, $limit = 12, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = ? AND active = 1 
                LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, description, price, category_id, image, stock, active) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssdssi",
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category_id'],
            $data['image'],
            $data['stock']
        );
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = ?, description = ?, price = ?, category_id = ?, image = ?, stock = ? 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssdssii",
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category_id'],
            $data['image'],
            $data['stock'],
            $id
        );
        
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET active = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE active = 1";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>

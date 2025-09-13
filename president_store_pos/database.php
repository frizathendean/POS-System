<?php
class DBConnection {
    private $conn;

    public function __construct() {
        $this->conn = new PDO(
            "mysql:host=localhost;dbname=president_store_pos",
            "root", "");
    }

    // PRODUCT FUNCTIONS
    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $result = $this->conn->prepare($sql);
        $result->execute([$id]);
        return $result->fetch();
    }
    
    public function searchProducts($term) {
        $sql = "SELECT * FROM products 
                WHERE id = ? OR name LIKE ? OR description LIKE ?";
        $result = $this->conn->prepare($sql);
        $searchTerm = "%$term%";
        $result->execute([$term, $searchTerm, $searchTerm]);
        return $result->fetchAll();
    }

    // TRANSACTION FUNCTIONS
    public function createTransaction($cashier_id, $customer_id = null) {
        $invoice_no = 'INV-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO transactions 
                (invoice_no, cashier_id, customer_id, status, created_at) 
                VALUES (?, ?, ?, 'unpaid', NOW())";
        $result = $this->conn->prepare($sql);
        $result->execute([$invoice_no, $cashier_id, $customer_id]);
        return $this->conn->lastInsertId();
    }

    public function addTransactionItem($transaction_id, $product_id, $qty, $price) {
        $sql = "INSERT INTO transaction_items 
                (transaction_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)";
        $result = $this->conn->prepare($sql);
        $result->execute([$transaction_id, $product_id, $qty, $price]);
    }

    public function getTransactionByInvoice($invoice_no) {
        $sql = "SELECT t.*, u.name as cashier_name 
                FROM transactions t
                JOIN users u ON t.cashier_id = u.id
                WHERE t.invoice_no = ?";
        $result = $this->conn->prepare($sql);
        $result->execute([$invoice_no]);
        return $result->fetch();
    }

    public function getTransactionItems($transaction_id) {
        $sql = "SELECT ti.*, p.name as product_name 
                FROM transaction_items ti
                JOIN products p ON ti.product_id = p.id
                WHERE ti.transaction_id = ?";
        $result = $this->conn->prepare($sql);
        $result->execute([$transaction_id]);
        return $result->fetchAll();
    }

    public function updateTransactionStatus($transaction_id, $status) {
        $sql = "UPDATE transactions SET status = ? WHERE id = ?";
        $result = $this->conn->prepare($sql);
        $result->execute([$status, $transaction_id]);
    }

    // REPORTING FUNCTIONS
    public function getTransactionsByStatus($status) {
        $sql = "SELECT t.*, u.name as cashier_name 
                FROM transactions t
                JOIN users u ON t.cashier_id = u.id
                WHERE t.status = ?
                ORDER BY t.created_at DESC";
        $result = $this->conn->prepare($sql);
        $result->execute([$status]);
        return $result->fetchAll();
    }
}
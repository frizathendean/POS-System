<?php
session_start();

// ------------------------
// DATABASE CONFIGURATION
// ------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'president_store_pos');
define('DB_USER', 'root');
define('DB_PASS', '');

// ------------------------
// APPLICATION SETTINGS
// ------------------------
define('APP_NAME', 'President Store POS');
define('BASE_URL', 'http://localhost/president_store_pos/');
define('TAX_RATE', 0.10); // 10% pajak

// ------------------------
// ERROR REPORTING
// ------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ------------------------
// DATABASE CONNECTION
// ------------------------
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ------------------------
// AUTHENTICATION & ROLES
// ------------------------
function checkAuth() {
    if (!isset($_SESSION['employee_id'])) { // Sesuai dengan session yang kamu set di login.php
        redirect('login.php');
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isCashier() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'cashier';
}

function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = "You don't have permission to access this page.";
        redirect('login.php');
    }
}

function requireCashier() {
    if (!isCashier()) {
        $_SESSION['error'] = "You don't have permission to access this page.";
        redirect('login.php');
    }
}

// ------------------------
// HELPER FUNCTIONS
// ------------------------
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// ------------------------
// PASSWORD UTILITY (Opsional)
// ------------------------
function hashPassword($plainPassword) {
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

function verifyPassword($plainPassword, $hashedPassword) {
    return password_verify($plainPassword, $hashedPassword);
}

?>



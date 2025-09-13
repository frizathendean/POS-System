<?php
require_once("../config.php");
checkAuth();

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Product ID not specified";
    header("Location: listProducts.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM transaction_items WHERE product_id = ?");
        $stmt->execute([$_GET['id']]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $_SESSION['error'] = "Cannot delete product - it has been used in transactions";
        } else {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $_SESSION['message'] = "Product deleted successfully";
        }

        header("Location: listProducts.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: listProducts.php");
        exit();
    }
}

include_once("../header.php");
include_once("../nav.php");
?>

<script src="https://cdn.tailwindcss.com"></script>
<div class="max-w-2xl mx-auto mt-10 bg-gray-800 text-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold mb-4 border-b border-gray-700 pb-2">Delete Product</h2>

    <div class="bg-yellow-600 text-yellow-100 p-4 rounded-lg mb-6">
        <p class="text-sm">⚠️ Are you sure you want to delete this product? This action <span class="font-semibold">cannot be undone</span>.</p>
    </div>

    <form method="post" class="flex gap-4">
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            Confirm Delete
        </button>
        <a href="listProducts.php" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-lg transition">
            Cancel
        </a>
    </form>
</div>

<?php include_once("../footer.php"); ?>

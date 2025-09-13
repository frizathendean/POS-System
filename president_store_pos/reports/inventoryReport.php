<?php
require_once("../config.php");
checkAuth();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

// Data
$stmt = $db->query("SELECT category, COUNT(*) as product_count, SUM(stock_quantity) as total_stock, SUM(stock_quantity * price) as inventory_value FROM products GROUP BY category ORDER BY category");
$categorySummary = $stmt->fetchAll();

$stmt = $db->query("SELECT * FROM products WHERE stock_quantity < 5 ORDER BY stock_quantity ASC");
$lowStockItems = $stmt->fetchAll();

$stmt = $db->query("SELECT * FROM products WHERE stock_quantity = 0 ORDER BY name");
$outOfStockItems = $stmt->fetchAll();

include_once("../header.php");
include_once("../nav.php");
?>

<div id="pageContent" class="min-h-screen bg-gray-900 text-white p-6 opacity-0 translate-y-10 transition-all duration-700">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Inventory by Category -->
        <div class="bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-xl font-semibold mb-6">Inventory by Category</h2>
            <div class="overflow-x-auto">
                <table class="table-auto w-full text-base text-left text-gray-300 border-collapse">
                    <thead class="text-sm uppercase bg-gray-700 text-gray-400">
                        <tr>
                            <th class="px-6 py-3 border border-gray-600 text-left">Category</th>
                            <th class="px-6 py-3 text-left border border-gray-600">Products</th>
                            <th class="px-6 py-3 text-left border border-gray-600">Stock</th>
                            <th class="px-6 py-3 text-left border border-gray-600">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorySummary as $category): ?>
                        <tr class="border-b border-gray-700">
                            <td class="px-6 py-3 border border-gray-600"><?= ucfirst($category['category']) ?></td>
                            <td class="px-6 py-3 text-left border border-gray-600"><?= $category['product_count'] ?></td>
                            <td class="px-6 py-3 text-left border border-gray-600"><?= $category['total_stock'] ?></td>
                            <td class="px-6 py-3 text-left border border-gray-600">Rp <?= number_format($category['inventory_value'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="space-y-8">
            <div class="bg-yellow-600 rounded-xl shadow-lg p-8">
                <h2 class="text-xl font-semibold mb-6">Low Stock Items (&lt;5)</h2>
                <?php if (empty($lowStockItems)): ?>
                    <p class="text-sm text-white/70">No low stock items.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-base text-left text-white border-collapse">
                            <thead class="text-sm uppercase bg-yellow-700 text-white/80">
                                <tr>
                                    <th class="px-6 py-3 border border-yellow-600 text-left">Product</th>
                                    <th class="px-6 py-3 text-left border border-yellow-600">Stock</th>
                                    <th class="px-6 py-3 text-left border border-yellow-600">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockItems as $product): ?>
                                <tr class="border-b border-yellow-700">
                                    <td class="px-6 py-3 border border-yellow-600"><?= $product['name'] ?></td>
                                    <td class="px-6 py-3 text-left font-bold text-red-200 border border-yellow-600"><?= $product['stock_quantity'] ?></td>
                                    <td class="px-6 py-3 text-left border border-yellow-600">Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Out of Stock -->
            <div class="bg-red-600 rounded-xl shadow-lg p-8">
                <h2 class="text-xl font-semibold mb-6">Out of Stock Items</h2>
                <?php if (empty($outOfStockItems)): ?>
                    <p class="text-sm text-white/70">No out of stock items.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-base text-left text-white border-collapse">
                            <thead class="text-sm uppercase bg-red-700 text-white/80">
                                <tr>
                                    <th class="px-6 py-3 border border-red-600 text-left">Product</th>
                                    <th class="px-6 py-3 text-left border border-red-600">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($outOfStockItems as $product): ?>
                                <tr class="border-b border-red-700">
                                    <td class="px-6 py-3 border border-red-600"><?= $product['name'] ?></td>
                                    <td class="px-6 py-3 text-left border border-red-600">Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const content = document.getElementById('pageContent');
        content.classList.remove('opacity-0', 'translate-y-10');
    });
</script>

<?php include_once("../footer.php"); ?>

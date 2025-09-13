<?php
require_once("../config.php");
checkAuth();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

// Sales Summary
$stmt = $db->prepare("SELECT 
    DATE(created_at) as sale_date,
    COUNT(*) as transaction_count,
    SUM(total_amount) as total_sales,
    SUM(tax_amount) as total_tax,
    SUM(total_amount - tax_amount) as net_sales
    FROM transactions
    WHERE status = 'paid'
    AND DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY sale_date DESC");
$stmt->execute([$dateFrom, $dateTo]);
$dailySales = $stmt->fetchAll();

// Total Summary
$stmt = $db->prepare("SELECT 
    COUNT(*) as transaction_count,
    SUM(total_amount) as total_sales,
    SUM(tax_amount) as total_tax,
    SUM(total_amount - tax_amount) as net_sales
    FROM transactions
    WHERE status = 'paid'
    AND DATE(created_at) BETWEEN ? AND ?");
$stmt->execute([$dateFrom, $dateTo]);
$summary = $stmt->fetch();

// Top Products
$stmt = $db->prepare("SELECT 
    p.name,
    SUM(ti.quantity) as total_quantity,
    SUM(ti.quantity * ti.price) as total_revenue
    FROM transaction_items ti
    JOIN products p ON ti.product_id = p.id
    JOIN transactions t ON ti.transaction_id = t.id
    WHERE t.status = 'paid'
    AND DATE(t.created_at) BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY total_quantity DESC
    LIMIT 5");
$stmt->execute([$dateFrom, $dateTo]);
$topProducts = $stmt->fetchAll();

include_once("../header.php");
include_once("../nav.php");
?>

<div id="pageContent" class="min-h-screen bg-gray-900 text-white p-6 opacity-0 translate-y-10 transition-all duration-700">
    <!-- Filter -->
    <div class="bg-gray-800 p-8 rounded mb-8">
        <form class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <div>
                <label for="date_from" class="block text-lg font-semibold mb-1">From</label>
                <input type="date" name="date_from" id="date_from" value="<?= $dateFrom ?>"
                    class="w-full bg-gray-900 text-white border border-gray-700 rounded px-4 py-3">
            </div>
            <div>
                <label for="date_to" class="block text-lg font-semibold mb-1">To</label>
                <input type="date" name="date_to" id="date_to" value="<?= $dateTo ?>"
                    class="w-full bg-gray-900 text-white border border-gray-700 rounded px-4 py-3">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-600 p-8 rounded shadow mb-6"> <!-- Added margin bottom -->
            <p class="text-lg font-semibold mb-4 border-b-2 border-white-600">Transactions</p> <!-- Added border-bottom -->
            <p class="text-4xl font-bold"><?= $summary['transaction_count'] ?? 0 ?></p>
        </div>
        <div class="bg-green-600 p-8 rounded shadow mb-6"> <!-- Added margin bottom -->
            <p class="text-lg font-semibold mb-4 border-b-2 border-white-600">Total Sales</p> <!-- Added border-bottom -->
            <p class="text-4xl font-bold">Rp <?= number_format($summary['total_sales'] ?? 0, 0, ',', '.') ?></p>
        </div>
        <div class="bg-cyan-600 p-8 rounded shadow mb-6"> <!-- Added margin bottom -->
            <p class="text-lg font-semibold mb-4 border-b-2 border-white-600">Tax Collected</p> <!-- Added border-bottom -->
            <p class="text-4xl font-bold">Rp <?= number_format($summary['total_tax'] ?? 0, 0, ',', '.') ?></p>
        </div>
        <div class="bg-yellow-600 p-8 rounded shadow mb-6"> <!-- Added margin bottom -->
            <p class="text-lg font-semibold mb-4 border-b-2 border-white-600">Net Sales</p> <!-- Added border-bottom -->
            <p class="text-4xl font-bold">Rp <?= number_format($summary['net_sales'] ?? 0, 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- Daily Sales -->
    <div class="bg-gray-800 p-6 rounded mb-8 border-l-4 border-blue-500 shadow">
        <h2 class="text-3xl font-bold mb-6 text-blue-400">📊 Daily Sales</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead class="text-gray-100 font-semibold text-lg bg-gray-700 border-b-2 border-gray-600">
                    <tr>
                        <th class="py-6 px-8 border-r-2 border-gray-600">Date</th>
                        <th class="py-6 px-8 border-r-2 border-gray-600">Transactions</th>
                        <th class="py-6 px-8 border-r-2 border-gray-600">Sales</th>
                        <th class="py-6 px-8 border-r-2 border-gray-600">Tax</th>
                        <th class="py-6 px-8 border-gray-600">Net</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailySales as $day): ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="py-6 px-8"><?= date('d M Y', strtotime($day['sale_date'])) ?></td>
                        <td class="py-6 px-8"><?= $day['transaction_count'] ?></td>
                        <td class="py-6 px-8">Rp <?= number_format($day['total_sales'], 0, ',', '.') ?></td>
                        <td class="py-6 px-8">Rp <?= number_format($day['total_tax'], 0, ',', '.') ?></td>
                        <td class="py-6 px-8">Rp <?= number_format($day['net_sales'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-gray-800 p-6 rounded border-l-4 border-yellow-500 shadow">
        <h2 class="text-3xl font-bold mb-6 text-yellow-400">🔥 Top Selling Products</h2>
        <table class="w-full text-center border-collapse">
            <thead class="text-gray-100 font-semibold text-lg bg-gray-700 border-b-2 border-gray-600">
                <tr>
                    <th class="py-6 px-8 border-r-2 border-gray-600">Product</th>
                    <th class="py-6 px-8 border-r-2 border-gray-600">Qty</th>
                    <th class="py-6 px-8 border-gray-600">Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topProducts as $product): ?>
                <tr class="border-b border-gray-700 hover:bg-gray-700">
                    <td class="py-6 px-8"><?= $product['name'] ?></td>
                    <td class="py-6 px-8"><?= $product['total_quantity'] ?></td>
                    <td class="py-6 px-8">Rp <?= number_format($product['total_revenue'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const content = document.getElementById('pageContent');
        content.classList.remove('opacity-0', 'translate-y-10');
    });
</script>

<?php include_once("../footer.php"); ?>

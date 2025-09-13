<?php
require_once("config.php");
checkAuth();

// Statistik
$stmt = $db->prepare("SELECT COUNT(*) as count, SUM(total_amount) as total 
                     FROM transactions 
                     WHERE DATE(created_at) = CURDATE() AND status = 'paid'");
$stmt->execute();
$todaySales = $stmt->fetch();

$stmt = $db->prepare("SELECT SUM(total_amount) as total
                     FROM transactions
                     WHERE MONTH(created_at) = MONTH(CURDATE())
                     AND YEAR(created_at) = YEAR(CURDATE())
                     AND status = 'paid'");
$stmt->execute();
$monthlySales = $stmt->fetch();

$stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE stock_quantity < 5");
$stmt->execute();
$lowStockCount = $stmt->fetch();

include_once("header.php");
include_once("nav.php");
?>

<div id="pageContent" class="min-h-screen bg-gray-900 text-white p-6 opacity-0 translate-y-10 transition-all duration-700">
    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-blue-700 rounded-xl shadow p-6 min-h-[180px] flex flex-col justify-center items-start text-left space-y-4">
        <h2 class="text-lg font-semibold text-white/80">Today's Sales</h2>
        <hr class="border-blue-300 opacity-50 w-full">
        <div class="text-2xl font-bold"><?= formatCurrency($todaySales['total'] ?? 0) ?></div>
        <div class="text-sm text-white/70"><?= $todaySales['count'] ?? 0 ?> transactions</div>
    </div>

    <div class="bg-green-700 rounded-xl shadow p-6 min-h-[180px] flex flex-col justify-center items-start text-left space-y-4">
        <h2 class="text-lg font-semibold text-white/80">Monthly Sales</h2>
        <hr class="border-green-300 opacity-50 w-full">
        <div class="text-2xl font-bold"><?= formatCurrency($monthlySales['total'] ?? 0) ?></div>
        <div class="text-sm text-white/70">Current month</div>
    </div>

    <div class="bg-red-600 rounded-xl shadow p-6 min-h-[180px] flex flex-col justify-center items-start text-left space-y-4">
        <h2 class="text-lg font-semibold text-white/80">Low Stock Items</h2>
        <hr class="border-red-300 opacity-50 w-full">
        <div class="text-2xl font-bold"><?= $lowStockCount['count'] ?? 0 ?></div>
        <div class="text-sm text-white/70">Items need restocking</div>
    </div>
</div>

    <!-- Tabel -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-gray-800 shadow rounded-xl p-6">
            <h3 class="text-xl font-semibold mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table-auto w-full text-base text-left text-gray-300 border border-gray-600">
                    <thead class="text-sm text-gray-400 uppercase bg-gray-700">
                        <tr class="divide-x divide-gray-600">
                            <th class="px-6 py-4">Invoice</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php
                        $stmt = $db->prepare("SELECT invoice_no, created_at, total_amount 
                                            FROM transactions 
                                            WHERE status = 'paid'
                                            ORDER BY created_at DESC 
                                            LIMIT 5");
                        $stmt->execute();
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr class="hover:bg-gray-700 transition divide-x divide-gray-700">
                            <td class="px-6 py-4"><?= $row['invoice_no'] ?></td>
                            <td class="px-6 py-4"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            <td class="px-6 py-4"><?= formatCurrency($row['total_amount']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-gray-800 shadow rounded-xl p-6">
            <h3 class="text-xl font-semibold mb-4">Top Selling Products</h3>
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table-auto w-full text-base text-left text-gray-300 border border-gray-600">
                    <thead class="text-sm text-gray-400 uppercase bg-gray-700">
                        <tr class="divide-x divide-gray-600">
                            <th class="px-6 py-4">Product</th>
                            <th class="px-6 py-4">Qty Sold</th>
                            <th class="px-6 py-4">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php
                        $stmt = $db->prepare("SELECT p.name, SUM(ti.quantity) as qty, SUM(ti.quantity * ti.price) as revenue
                                            FROM transaction_items ti
                                            JOIN products p ON ti.product_id = p.id
                                            JOIN transactions t ON ti.transaction_id = t.id
                                            WHERE t.status = 'paid'
                                            GROUP BY p.id
                                            ORDER BY qty DESC
                                            LIMIT 5");
                        $stmt->execute();
                        while ($row = $stmt->fetch()):
                        ?>
                        <tr class="hover:bg-gray-700 transition divide-x divide-gray-700">
                            <td class="px-6 py-4"><?= $row['name'] ?></td>
                            <td class="px-6 py-4"><?= $row['qty'] ?></td>
                            <td class="px-6 py-4"><?= formatCurrency($row['revenue']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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

<?php include_once("footer.php"); ?>

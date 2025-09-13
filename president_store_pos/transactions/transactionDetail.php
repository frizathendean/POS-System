<?php
require_once("../config.php");
checkAuth();

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Transaction ID not specified";
    header("Location: listTransactions.php");
    exit();
}

$stmt = $db->prepare("SELECT t.*, u.name as cashier_name 
                     FROM transactions t
                     JOIN employees u ON t.cashier_id = u.id
                     WHERE t.id = ?");
$stmt->execute([$_GET['id']]);
$transaction = $stmt->fetch();

if (!$transaction) {
    $_SESSION['error'] = "Transaction not found";
    header("Location: listTransactions.php");
    exit();
}

$stmt = $db->prepare("SELECT ti.*, p.name as product_name, p.product_id as product_code
                     FROM transaction_items ti
                     JOIN products p ON ti.product_id = p.id
                     WHERE ti.transaction_id = ?");
$stmt->execute([$_GET['id']]);
$items = $stmt->fetchAll();

include_once("../header.php");
include_once("../nav.php");
?>

<div class="max-w-7xl mx-auto p-6 text-white">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Transaction Detail</h2>
        <a href="listTransactions.php" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm">← Back to List</a>
    </div>

    <div class="bg-gray-800 rounded-lg shadow mb-6">
        <div class="border-b border-gray-700 px-6 py-4">
            <h3 class="text-lg font-semibold">Invoice #<?= $transaction['invoice_no'] ?></h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p><span class="font-semibold">Date:</span> <?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></p>
                <p><span class="font-semibold">Cashier:</span> <?= $transaction['cashier_name'] ?></p>
            </div>
            <div>
                <p>
                    <span class="font-semibold">Status:</span>
                    <span class="inline-block px-2 py-1 rounded text-sm font-medium 
                        <?= $transaction['status'] == 'paid' ? 'bg-green-600' : 'bg-yellow-500 text-black' ?>">
                        <?= ucfirst($transaction['status']) ?>
                    </span>
                </p>
                <p><span class="font-semibold">Payment Method:</span> <?= ucfirst(str_replace('_', ' ', $transaction['payment_method'])) ?></p>
                <?php if ($transaction['notes']): ?>
                <p><span class="font-semibold">Notes:</span> <?= htmlspecialchars($transaction['notes']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg shadow mb-6">
        <div class="border-b border-gray-700 px-6 py-4">
            <h3 class="text-xl font-semibold">Products</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full text-white">
                <thead class="bg-gray-700 text-gray-200">
                    <tr>
                        <th class="text-left py-3 px-6 text-base">Product Code</th>
                        <th class="text-left py-3 px-6 text-base">Product Name</th>
                        <th class="text-right py-3 px-6 text-base">Price</th>
                        <th class="text-right py-3 px-6 text-base">Qty</th>
                        <th class="text-right py-3 px-6 text-base">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr class="border-b border-gray-700">
                        <td class="py-3 px-6 text-base"><?= $item['product_code'] ?></td>
                        <td class="py-3 px-6 text-base"><?= $item['product_name'] ?></td>
                        <td class="py-3 px-6 text-right text-base">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="py-3 px-6 text-right text-base"><?= $item['quantity'] ?></td>
                        <td class="py-3 px-6 text-right text-base">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right py-3 px-6 text-base">Subtotal:</th>
                        <th class="text-right py-3 px-6 text-base">Rp <?= number_format($transaction['total_amount'] - $transaction['tax_amount'], 0, ',', '.') ?></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right py-3 px-6 text-base">Tax (10%):</th>
                        <th class="text-right py-3 px-6 text-base">Rp <?= number_format($transaction['tax_amount'], 0, ',', '.') ?></th>
                    </tr>
                    <tr class="bg-gray-700 font-semibold">
                        <th colspan="4" class="text-right py-3 px-6 text-base">Total:</th>
                        <th class="text-right py-3 px-6 text-base">Rp <?= number_format($transaction['total_amount'], 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if ($_SESSION['role'] == 'admin' && $transaction['status'] == 'unpaid'): ?>
    <div class="text-right">
        <a href="markPaid.php?id=<?= $transaction['id'] ?>" class="inline-block px-5 py-2 bg-green-600 hover:bg-green-500 text-white rounded shadow">
            Mark as Paid
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include_once("../footer.php"); ?>
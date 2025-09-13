<?php 
require_once("../config.php");
checkAuth();

$stmt = $db->query("SELECT id, invoice_no, total_amount, created_at FROM transactions ORDER BY created_at DESC");
$transactions = $stmt->fetchAll();

include_once("../header.php");
include_once("../nav.php");
?>

<div id="pageContent" class="min-h-screen bg-gray-900 text-white p-6 opacity-0 translate-y-10 transition-all duration-700">
    <div class="w-full h-full max-w-full mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white">Transactions</h1>
            <a href="newTransaction.php" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-sm font-medium shadow transition">
                + New Transaction
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-6 px-4 py-3 bg-emerald-700 text-white border border-emerald-500 rounded-md shadow">
                <?= $_SESSION['message'] ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="bg-gray-800 rounded-xl shadow overflow-x-auto border border-gray-700">
            <table class="w-full text-lg text-left text-white">
                <thead class="bg-gray-700 text-sm uppercase font-semibold tracking-wide">
                    <tr>
                        <th class="px-8 py-4 border-b border-gray-600">Invoice No</th>
                        <th class="px-8 py-4 border-b border-gray-600">Total</th>
                        <th class="px-8 py-4 border-b border-gray-600">Date</th>
                        <th class="px-8 py-4 border-b border-gray-600">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($transactions) > 0): ?>
                        <?php foreach ($transactions as $i => $transaction): ?>
                            <tr class="<?= $i % 2 === 0 ? 'bg-gray-800' : 'bg-gray-900' ?> hover:bg-gray-700 transition">
                                <td class="px-8 py-6 border-b border-gray-700"><?= htmlspecialchars($transaction['invoice_no']) ?></td>
                                <td class="px-8 py-6 border-b border-gray-700">Rp<?= number_format($transaction['total_amount'], 0, ',', '.') ?></td>
                                <td class="px-8 py-6 border-b border-gray-700"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
                                <td class="px-8 py-6 border-b border-gray-700">
                                    <a href="transactionDetail.php?id=<?= $transaction['id'] ?>" 
                                       class="inline-block bg-blue-700 hover:bg-emerald-600 text-white px-6 py-3 rounded-md text-sm font-semibold transition duration-200 shadow">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-8 py-6 text-center text-gray-400">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

<?php 
require_once("../config.php");
checkAuth();

$cashier_id = $_SESSION['employee_db_id'] ?? null;

// Debug if session is not available
if (!$cashier_id) {
    error_log("❌ cashier_id (employee_db_id) not found in session.");
    $_SESSION['error'] = "Failed to get cashier info. Please log in again.";
    redirect("logout.php");
}
error_log("✅ cashier_id (employee_db_id): " . $cashier_id);

$transaction = [
    'payment_method' => 'cash',
    'notes' => ''
];
$items = [];
$errors = [];

// Fetch product data
$stmt = $db->query("SELECT id, product_id, name, price, stock_quantity FROM products ORDER BY name");
$products = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction['payment_method'] = $_POST['payment_method'];
    $transaction['notes'] = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    if (isset($_POST['product_id']) && is_array($_POST['product_id'])) {
        foreach ($_POST['product_id'] as $index => $productId) {
            if (!empty($productId) && !empty($_POST['quantity'][$index])) {
                $quantity = (int)$_POST['quantity'][$index];
                if ($quantity > 0) {
                    $items[] = [
                        'product_id' => $productId,
                        'quantity' => $quantity
                    ];
                }
            }
        }
    }

    if (empty($items)) {
        $errors[] = "At least one product must be added to the transaction.";
    }

    foreach ($items as $item) {
        $product = array_filter($products, fn($p) => $p['id'] == $item['product_id']);
        $product = reset($product);
        if ($product['stock_quantity'] < $item['quantity']) {
            $errors[] = "Insufficient stock for {$product['name']} (Available: {$product['stock_quantity']})";
        }
    }

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $stmt = $db->prepare("INSERT INTO transactions (invoice_no, cashier_id, status, payment_method, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $invoiceNo,
                $cashier_id,
                'paid',
                $transaction['payment_method'],
                $transaction['notes']
            ]);
            $transactionId = $db->lastInsertId();

            $totalAmount = 0;
            foreach ($items as $item) {
                $product = array_filter($products, fn($p) => $p['id'] == $item['product_id']);
                $product = reset($product);

                $subtotal = $product['price'] * $item['quantity'];
                $totalAmount += $subtotal;

                $stmt = $db->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $transactionId,
                    $product['id'],
                    $item['quantity'],
                    $product['price']
                ]);

                $stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $product['id']]);
            }

            $taxAmount = $totalAmount * TAX_RATE;
            $stmt = $db->prepare("UPDATE transactions SET total_amount = ?, tax_amount = ? WHERE id = ?");
            $stmt->execute([$totalAmount, $taxAmount, $transactionId]);

            $db->commit();

            $_SESSION['message'] = "Transaction completed successfully. Invoice: $invoiceNo";
            redirect("transactions/transactionDetail.php?id=$transactionId");
        } catch (PDOException $e) {
            $db->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include_once("../header.php");
include_once("../nav.php");
?>

<!-- HTML START -->
<div id="pageContent" class="min-h-screen bg-gray-900 text-white p-6 opacity-0 translate-y-10 transition-all duration-700">
    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded bg-red-700 bg-opacity-30 text-red-300 border border-red-500">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" id="transactionForm" class="space-y-8">
        <!-- Payment & Summary -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Payment -->
            <div class="bg-gray-900 p-8 rounded shadow min-h-64">
                <div class="bg-gray-800 border-l-4 border-blue-500 p-4 mb-5">
                    <h3 class="text-lg font-semibold text-white">Payment & Notes</h3>
                </div>
                <label class="block mb-3 font-medium" for="payment_method">Payment Method</label>
                <select name="payment_method" id="payment_method" required class="w-full p-3 bg-gray-800 border border-gray-700 rounded text-white">
                    <option value="cash" <?= $transaction['payment_method'] == 'cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="credit_card" <?= $transaction['payment_method'] == 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                    <option value="debit_card" <?= $transaction['payment_method'] == 'debit_card' ? 'selected' : '' ?>>Debit Card</option>
                    <option value="transfer" <?= $transaction['payment_method'] == 'transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                </select>
                <div class="mt-5">
                    <label class="block mb-3 font-medium" for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="w-full p-3 bg-gray-800 border border-gray-700 rounded text-white h-24"><?= htmlspecialchars($transaction['notes']) ?></textarea>
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-gray-900 p-8 rounded shadow min-h-64">
                <div class="bg-gray-800 border-l-4 border-purple-500 p-4 mb-5">
                    <h3 class="text-lg font-semibold text-white">Transaction Summary</h3>
                </div>
                <div id="transactionSummary" class="p-4">
                    <p class="text-gray-400">Add products to see summary</p>
                </div>
            </div>
        </div>

        <!-- Product Items -->
        <div class="bg-gray-900 p-8 rounded shadow">
            <div class="bg-gray-800 border-l-4 border-yellow-500 p-4 mb-5">
                <h3 class="text-lg font-semibold text-white">Products</h3>
            </div>

            <div class="flex justify-end mb-5">
                <button type="button" id="addProductBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">+ Add Product</button>
            </div>

            <div id="productItems" class="space-y-5">
                <div class="grid md:grid-cols-12 gap-4 product-row">
                    <div class="md:col-span-6">
                        <select name="product_id[]" class="product-select w-full p-3 bg-gray-800 border border-gray-700 rounded text-white" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>" data-stock="<?= $product['stock_quantity'] ?>">
                                    <?= htmlspecialchars($product['name']) ?> (Rp <?= number_format($product['price'], 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <input type="number" name="quantity[]" class="quantity w-full p-3 bg-gray-800 border border-gray-700 rounded text-white" min="1" value="1" required>
                    </div>
                    <div class="md:col-span-2 flex items-center">
                        <button type="button" class="remove-product-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 mt-6">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-semibold px-8 py-3 rounded">Complete Transaction</button>
            <a href="listTransactions.php" class="bg-gray-700 hover:bg-gray-600 text-white font-medium px-8 py-3 rounded">Cancel</a>
        </div>
    </form>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productItems = document.getElementById('productItems');
    const addProductBtn = document.getElementById('addProductBtn');

    addProductBtn.addEventListener('click', () => {
        const newRow = document.createElement('div');
        newRow.className = 'grid md:grid-cols-12 gap-4 product-row';
        newRow.innerHTML = `<?= addslashes(
            preg_replace("/\s+/", " ", '
                <div class="md:col-span-6">
                    <select name="product_id[]" class="product-select w-full p-3 bg-gray-800 border border-gray-700 rounded text-white" required>
                        <option value="">Select Product</option>' . implode('', array_map(function ($product) {
                            return '<option value="' . $product['id'] . '" data-price="' . $product['price'] . '" data-stock="' . $product['stock_quantity'] . '">' . htmlspecialchars($product['name']) . ' (Rp ' . number_format($product['price'], 0, ',', '.') . ')</option>';
                        }, $products)) . '
                    </select>
                </div>
                <div class="md:col-span-4">
                    <input type="number" name="quantity[]" class="quantity w-full p-3 bg-gray-800 border border-gray-700 rounded text-white" min="1" value="1" required>
                </div>
                <div class="md:col-span-2 flex items-center">
                    <button type="button" class="remove-product-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Delete</button>
                </div>
            ')
        ) ?>`;
        productItems.appendChild(newRow);
        updateTransactionSummary();
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-product-btn')) {
            const row = e.target.closest('.product-row');
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                updateTransactionSummary();
            } else {
                alert('At least one product is required');
            }
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity')) {
            updateTransactionSummary();
        }
    });

    function updateTransactionSummary() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity');
            if (productSelect.value && quantityInput.value) {
                const price = parseFloat(productSelect.selectedOptions[0].dataset.price);
                const quantity = parseInt(quantityInput.value);
                total += price * quantity;
            }
        });

        const tax = total * 0.1;
        const grandTotal = total + tax;

        document.getElementById('transactionSummary').innerHTML = `
            <table class="w-full text-base">
                <tr>
                    <td class="font-medium text-white py-3">Subtotal:</td>
                    <td class="text-right text-white py-3">Rp ${total.toLocaleString('id-ID')}</td>
                </tr>
                <tr>
                    <td class="font-medium text-white py-3">Tax (10%):</td>
                    <td class="text-right text-white py-3">Rp ${tax.toLocaleString('id-ID')}</td>
                </tr>
                <tr class="border-t border-gray-700 font-bold bg-gray-800">
                    <td class="text-white py-4 px-3">Total:</td>
                    <td class="text-right text-yellow-400 py-4 px-3">Rp ${grandTotal.toLocaleString('id-ID')}</td>
                </tr>
            </table>
        `;
    }

    updateTransactionSummary();
});
</script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const content = document.getElementById('pageContent');
        content.classList.remove('opacity-0', 'translate-y-10');
    });
</script>

<?php include_once("../footer.php"); ?>
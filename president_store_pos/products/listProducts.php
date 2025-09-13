<?php
require_once("../config.php");
checkAuth();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$stmt = $db->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmt->fetchAll();

include_once("../header.php");
include_once("../nav.php");
?>

<div id="pageContent" class="min-h-screen bg-gray-900 text-white p-6 opacity-0 translate-y-10 transition-all duration-700">
    <!-- Add Product + Filter/Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <!-- Filter Section -->
    <div class="flex flex-col md:flex-row gap-4 flex-1">
        <!-- Search Input -->
        <input type="text" id="searchInput" placeholder="Search by name or category..."
            class="w-full md:w-1/2 px-4 py-2 rounded bg-gray-800 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

        <!-- Category Dropdown -->
        <select id="categoryFilter" class="w-full md:w-1/3 px-4 py-2 rounded bg-gray-800 border border-gray-600 text-white">
            <option value="">All Categories</option>
            <?php
            $catStmt = $db->query("SELECT DISTINCT category FROM products ORDER BY category");
            $categories = $catStmt->fetchAll();
            foreach ($categories as $cat) {
                echo "<option value=\"" . htmlspecialchars($cat['category']) . "\">" . htmlspecialchars($cat['category']) . "</option>";
            }
            ?>
        </select>

        <!-- Reset Button -->
        <button id="resetFilters" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded">
            Reset
        </button>
    </div>

    <!-- Add Product Button (now on the right) -->
    <a href="productForm.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded shadow shrink-0">
        + Add Product
    </a>
</div>


    <!-- Table Section -->
    <div class="bg-gray-800 p-6 rounded shadow overflow-x-auto">
        <table id="productTable" class="min-w-full text-base text-center text-white border-collapse">
            <thead class="bg-gray-600 text-base font-semibold text-gray-100">
                <tr>
                    <th class="py-3 px-5 border border-gray-500">Product ID</th>
                    <th class="py-3 px-5 border border-gray-500">Name</th>
                    <th class="py-3 px-5 border border-gray-500">Category</th>
                    <th class="py-3 px-5 border border-gray-500">Price</th>
                    <th class="py-3 px-5 border border-gray-500">Stock</th>
                    <th class="py-3 px-5 border border-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                        <td class="py-3 px-5 border border-gray-500"><?= $product['product_id'] ?></td>
                        <td class="py-3 px-5 border border-gray-500"><?= htmlspecialchars($product['name']) ?></td>
                        <td class="py-3 px-5 border border-gray-500"><?= htmlspecialchars($product['category']) ?></td>
                        <td class="py-3 px-5 border border-gray-500">Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                        <td class="py-3 px-5 border border-gray-500"><?= $product['stock_quantity'] ?? 0 ?></td>
                        <td class="py-3 px-5 border border-gray-500">
                            <div class="flex justify-center space-x-2">
                                <a href="productForm.php?id=<?= $product['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Edit</a>
                                <a href="deleteProducts.php?id=<?= $product['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-400">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const content = document.getElementById('pageContent');
        content.classList.remove('opacity-0', 'translate-y-10');

        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const resetButton = document.getElementById('resetFilters');
        const table = document.getElementById('productTable');
        const rows = table.querySelector('tbody').querySelectorAll('tr');

        function filterRows() {
            const searchQuery = searchInput.value.toLowerCase().trim();
            const selectedCategory = categoryFilter.value.toLowerCase();

            rows.forEach(row => {
                const productId = row.cells[0]?.textContent.toLowerCase() || '';
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                const category = row.cells[2]?.textContent.toLowerCase() || '';

                const matchProductId = productId.includes(searchQuery);
                const matchName = name.includes(searchQuery);
                const matchCategory = selectedCategory === '' || category === selectedCategory;

                row.style.display = ((matchProductId || matchName) && matchCategory) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterRows);
        categoryFilter.addEventListener('change', filterRows);

        resetButton.addEventListener('click', () => {
            searchInput.value = '';
            categoryFilter.value = '';
            filterRows();
        });
    });
</script>

<?php include_once("../footer.php"); ?>

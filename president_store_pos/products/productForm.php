<?php
require_once("../config.php");
checkAuth();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$isEdit = isset($_GET['id']);
$product = null;

if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $subcategory = $_POST['subcategory'] ?? '';
    $price = $_POST['price'] ?? 0;
    $cost = $_POST['cost'] ?? 0;
    $stock = $_POST['stock_quantity'] ?? 0;

    if (!$productId || !$name || !$category || $price === '' || $cost === '' || $stock === '') {
        $errors[] = "Please fill in all required fields.";
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $db->prepare("UPDATE products SET product_id=?, name=?, description=?, category=?, subcategory=?, price=?, cost=?, stock_quantity=? WHERE id=?");
            $stmt->execute([$productId, $name, $description, $category, $subcategory, $price, $cost, $stock, $_GET['id']]);
        } else {
            $stmt = $db->prepare("INSERT INTO products (product_id, name, description, category, subcategory, price, cost, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
 
            $stmt->execute([$productId, $name, $description, $category, $subcategory, $price, $cost, $stock]);
        }

        header("Location: listProducts.php");
        exit();
    }
}

include_once("../header.php");
include_once("../nav.php");
?>

<div class="max-w-6xl mx-auto p-6 text-white">
    <h2 class="text-2xl font-bold mb-4 text-blue-400"><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-700 p-3 rounded mb-4">
            <?php foreach ($errors as $error): ?>
                <p>⚠️ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="productForm" class="space-y-4 bg-gray-800 p-8 rounded shadow-lg">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block font-medium text-lg mb-1">Product ID*</label>
                <input type="text" name="product_id" required
                       value="<?= htmlspecialchars($product['product_id'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>

            <div>
                <label class="block font-medium text-lg mb-1">Name*</label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($product['name'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>
        </div>

        <!-- <div>
            <label class="block font-medium">Description</label>
            <textarea name="description"
                      class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div> -->

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block font-medium text-lg mb-1">Category*</label>
                <input type="text" name="category" required
                       value="<?= htmlspecialchars($product['category'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>

            <div>
                <label class="block font-medium text-lg mb-1">Subcategory</label>
                <input type="text" name="subcategory"
                       value="<?= htmlspecialchars($product['subcategory'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block font-medium text-lg mb-1">Price (Rp)*</label>
                <input type="number" step="0.01" name="price" required
                       value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>

            <div>
                <label class="block font-medium text-lg mb-1">Cost (Rp)*</label>
                <input type="number" step="0.01" name="cost" required
                       value="<?= htmlspecialchars($product['cost'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>

            <div>
                <label class="block font-medium text-lg mb-1">Stock*</label>
                <input type="number" name="stock_quantity" required
                       value="<?= htmlspecialchars($product['stock_quantity'] ?? '') ?>"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>
        </div>

        <div class="flex justify-between pt-6">
            <a href="listProducts.php"
               class="text-gray-400 hover:text-white underline text-lg">← Back to list</a>
            <button type="submit" id="submitButton"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg text-lg">
                <?= $isEdit ? 'Update Product' : 'Add Product' ?>
            </button>
        </div>
    </form>
</div>

<!-- Success Modal Popup -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 invisible transition-all duration-300 ease-in-out z-50">
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full transform scale-95 transition-transform duration-300 ease-in-out">
        <div class="text-center">
            <!-- Animated SVG Checkmark -->
            <div class="mx-auto mb-4 w-24 h-24 relative">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-white mb-3 opacity-0 transform translate-y-4 transition-all duration-500 delay-700">Success!</h3>
            <p class="text-gray-300 text-lg mb-6 opacity-0 transform translate-y-4 transition-all duration-500 delay-900" id="modalMessage">Product has been successfully updated.</p>
            <button id="closeModal" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg text-lg transition-colors duration-200 opacity-0 transform translate-y-4 transition-all duration-500 delay-1100">
                OK
            </button>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleIn {
    from { transform: scale(0.9); }
    to { transform: scale(1); }
}

.modal-show {
    opacity: 1 !important;
    visibility: visible !important;
}

.modal-show > div {
    transform: scale(1) !important;
}

.modal-show h3,
.modal-show p,
.modal-show button {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4ade80;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    animation-delay: 0.3s;
}

.checkmark {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: block;
    stroke-width: 4;
    stroke: #4ade80;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4ade80;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0px 0px 0px 30px transparent;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    const submitButton = document.getElementById('submitButton');
    const successModal = document.getElementById('successModal');
    const modalContent = successModal.querySelector('div');
    const closeModal = document.getElementById('closeModal');
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    
    if (isEdit) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (form.checkValidity()) {
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerText = 'Processing...';
                
                const formData = new FormData(form);
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        successModal.classList.add('modal-show');
                        
                        submitButton.disabled = false;
                        submitButton.innerText = 'Update Product';
                    } else {
                        throw new Error('Form submission failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.disabled = false;
                    submitButton.innerText = 'Update Product';
                    alert('An error occurred. Please try again.');
                });
            } else {
                // Let the browser handle invalid form
                form.reportValidity();
            }
        });
        
        closeModal.addEventListener('click', function() {
            successModal.classList.remove('modal-show');
            
            setTimeout(() => {
                window.location.href = 'listProducts.php';
            }, 300);
        });
    }
});
</script>

<?php include_once("../footer.php"); ?>
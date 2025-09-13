<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("config.php");
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

  body {
    font-family: 'Poppins', sans-serif;
  }

  .header {
    background-color: #2c3e50;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 40px;
    border-bottom: 1px solid #34495e;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

  .logo-container {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .logo-container img {
    width: 120px;
    height: 80px;
    margin-right: -20px;
  }

  .logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
  }

  .logo-title {
    font-family: 'Times New Roman', Times, serif;
    font-size: 35px;
    font-weight: bold;
    color: #3498db;
  }

  .logo-title span {
    color: #e74c3c;
  }

  .logo-subtitle {
    font-family: 'Times New Roman', Times, serif;
    font-size: 14px;
    color: #ecf0f1;
    text-align: center;
    margin-left: -20px;
  }

  .menu-container {
    display: flex;
    align-items: center;
  }

  .dropdown {
    position: relative;
    margin: 0 15px;
    font-weight: bold;
    font-size: 18px;
  }

  .dropdown a {
    text-decoration: none;
    color: #ecf0f1;
    padding: 10px 5px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .dropdown-arrow {
    width: 10px;
    height: 10px;
    transition: transform 0.3s ease;
    filter: brightness(0) invert(1);
  }

  .dropdown:hover .dropdown-arrow {
    transform: rotate(180deg);
  }

  .dropdown-content {
    opacity: 0;
    visibility: hidden;
    position: absolute;
    background-color: #34495e;
    min-width: 160px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.3);
    z-index: 1;
    top: 100%;
    left: 0;
    border-radius: 5px;
    transition: opacity 0.3s ease, visibility 0.3s ease;
  }

  .dropdown-content a {
    color: #ecf0f1;
    padding: 10px;
    text-decoration: none;
    display: block;
    transition: color 0.3s ease, background-color 0.3s ease;
    border-top: 1px solid #445566;
  }

  .dropdown-content a:hover {
    background-color: #445566;
    color: #e74c3c;
  }

  .dropdown:hover .dropdown-content {
    opacity: 1;
    visibility: visible;
  }

  .user-info-centered {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 13px;
    line-height: 1.2;
    margin-left: 30px;
    color: #ecf0f1;
  }

  .user-info-centered div:first-child {
    font-weight: bold;
    font-size: 14px;
  }

  .logout-btn {
    font-size: 14px;
    font-weight: 600;
    padding: 8px 14px;
    background-color: #c0392b;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-left: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: background-color 0.3s ease, transform 0.2s ease;
  }

  .logout-btn:hover {
    background-color: #e74c3c;
    transform: scale(1.03);
  }

  .transaction-link {
    font-weight: bold;
    font-size: 18px;
    text-decoration: none;
    color: rgb(251, 253, 253);
    margin-right: 15px;
    padding: 8px 12px;
    border-radius: 5px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .transaction-link:before {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0%;
    height: 2px;
    background-color: #e74c3c;
    transition: width 0.3s ease;
  }

  .transaction-link:hover {
    color: #3498db;
  }

  .transaction-link:hover:before {
    width: 100%;
  }

  .transaction-link:active {
    transform: scale(0.95);
  }

  /* Modal Styles */
  #logoutModal {
    transition: all 0.3s ease;
  }

  .modal-hidden {
    opacity: 0;
    transform: scale(0.95);
    pointer-events: none;
  }

  .modal-visible {
    opacity: 1;
    transform: scale(1);
    pointer-events: auto;
  }

  #logoutModal .bg-white {
    background: rgba(34, 44, 53, 0.65);
    backdrop-filter: blur(10px);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  #logoutModal .bg-white * {
    color: #fff !important;
  }
</style>

<div class="header">
  <a href="<?= BASE_URL ?>dashboard.php" class="logo-link">
    <div class="logo-container">
      <img src="<?= BASE_URL ?>assets/logo.png" alt="Logo">
      <div class="logo-text">
        <div class="logo-title">President University <span>Store</span></div>
        <div class="logo-subtitle">
          <span style="color: #3498db;">Smart Shopping,</span>
          <span style="color: #e74c3c;">Presidential Choice</span>
        </div>
      </div>
    </div>
  </a>

  <div class="menu-container">
    <?php if (isAdmin()): ?>
      <div class="dropdown">
        <a href="#">INVENTORY <img class="dropdown-arrow" src="https://cdn-icons-png.flaticon.com/512/60/60995.png"></a>
        <div class="dropdown-content">
          <a href="<?= BASE_URL ?>products/listProducts.php">Products</a>
        </div>
      </div>

      <div class="dropdown">
        <a href="#">REPORTS <img class="dropdown-arrow" src="https://cdn-icons-png.flaticon.com/512/60/60995.png"></a>
        <div class="dropdown-content">
          <a href="<?= BASE_URL ?>reports/inventoryReport.php">Inventory Report</a>
          <a href="<?= BASE_URL ?>reports/salesReport.php">Sales Report</a>
        </div>
      </div>
    <?php elseif (isCashier()): ?>
      <a href="<?= BASE_URL ?>transactions/listTransactions.php" class="transaction-link">
        List Transactions
      </a>
    <?php endif; ?>

    <?php if (isset($_SESSION['employee_id'])): ?>
      <div class="user-info-centered">
        <div><?= htmlspecialchars($_SESSION['name']) ?></div>
        <div><?= htmlspecialchars($_SESSION['employee_id']) ?></div>
      </div>
      <button class="logout-btn" onclick="openLogoutModal()">Logout</button>
    <?php else: ?>
      <a class="nav-link" href="<?= BASE_URL ?>login.php">Login</a>
    <?php endif; ?>
  </div>
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center modal-hidden">
  <div class="bg-white p-6 rounded-lg shadow-xl text-center" style="min-width: 300px;">
    <h2 class="text-xl font-bold mb-4">Are you sure you want to logout?</h2>
    <form method="POST" action="<?= BASE_URL ?>logout.php">
      <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-all duration-200 mr-2">Yes</button>
      <button type="button" onclick="closeLogoutModal()" class="bg-yellow-300 text-gray-900 px-4 py-2 rounded hover:bg-yellow-400 transition-all duration-200">Cancel</button>
    </form>
  </div>
</div>

<script>
  const logoutModal = document.getElementById('logoutModal');

  function openLogoutModal() {
    logoutModal.classList.remove('modal-hidden');
    logoutModal.classList.add('modal-visible');
  }

  function closeLogoutModal() {
    logoutModal.classList.remove('modal-visible');
    logoutModal.classList.add('modal-hidden');
  }
</script>

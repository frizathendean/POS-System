<?php
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['employee_id'], $_POST['password'])) {
    header('Content-Type: application/json');

    $employee_id = trim($_POST['employee_id']);
    $password = $_POST['password'];

    try {
        $stmt = $db->prepare("SELECT * FROM employees WHERE employee_id = ?");
        $stmt->execute([$employee_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['employee_db_id'] = $user['id']; 

            echo json_encode([
                'success' => true,
                'role' => $user['role']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid employee ID or password'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Employee Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .bg-image {
      background: url('https://gotgarms.com/wp-content/uploads/2022/03/hoodies.jpg') no-repeat center center;
      background-size: cover;
      position: relative;
    }
    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 10rem;
      color: white;
    }
    #loadingSpinner {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.6);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-yellow-400">
  <div id="loadingSpinner" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 flex-col">
    <div class="animate-spin inline-block w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mb-4"></div>
    <p class="text-white font-semibold text-lg">Loading...</p>
  </div>

  <div class="flex w-full h-screen bg-white shadow-xl overflow-hidden">
    <!-- Left Side -->
    <div class="relative w-2/3 bg-image">
      <div class="overlay">
        <h1 class="text-5xl font-bold">President University Store</h1>
        <p class="text-lg text-gray-300 mt-2">Smart Shopping, Presidential Choice</p>
        <p class="mt-4 text-gray-200 text-justify leading-relaxed">
          Welcome to the President University Store Point of Sale System. This platform is designed exclusively for authorized staff members to manage all aspects of the PU Store’s daily operations.
          From processing sales transactions, updating product inventories, to generating detailed reports everything you need is right here.
          Please log in using your employee credentials to access the system and ensure a smooth, efficient shopping experience for our campus community.
        </p>
      </div>
    </div>

    <!-- Right Side (Login Form) -->
    <div class="w-1/3 p-10 flex flex-col justify-center text-center border-l border-gray-200 shadow-lg z-10 bg-white">
      <img src="assets/PU store.png" class="w-40 mx-auto mb-4" alt="PU Store Logo" />
      <h2 class="text-lg font-semibold text-blue-900">Login to President University Store System</h2>
      <p class="text-gray-700 mb-6 text-sm">Authorized access only. Please enter your credentials below.</p>

      <p id="errorMsg" class="text-red-500 text-sm mb-4 hidden"></p>

      <form id="loginForm" class="space-y-4">
        <div class="relative">
          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">&#128100;</span>
          <input type="text" name="employee_id" id="employee_id" placeholder="Enter Your Employee ID..." required
            class="w-full pl-10 py-3 border border-blue-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>
        <div class="relative">
          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">&#128274;</span>
          <input type="password" name="password" id="password" placeholder="Enter Your Password..." required
            class="w-full pl-10 py-3 border border-blue-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>
        <button type="submit"
          class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition-all">LOG IN</button>
        <p class="text-sm text-gray-700 mt-2 cursor-pointer hover:text-red-600">Forgot your password?</p>
      </form>

      <footer class="mt-8 text-xs text-gray-400">
        &copy; 2025 President University Store. All rights reserved.
      </footer>
    </div>
  </div>
  <script>
  const form = document.getElementById('loginForm');
  const spinner = document.getElementById('loadingSpinner');
  const errorMsg = document.getElementById('errorMsg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    spinner.style.display = 'flex';
    errorMsg.classList.add('hidden');

    const formData = new FormData(form);

    try {
      const response = await fetch(window.location.href, {
        method: 'POST',
        body: formData
      });

      const text = await response.text();
      console.log("Raw response:", text);
      const data = JSON.parse(text);

      if (data.success) {
        setTimeout(() => {
          if (data.role === 'admin') {
            window.location.href = 'dashboard.php';
          } else if (data.role === 'cashier') {
            window.location.href = 'transactions/newTransaction.php'; 
          }
        }, 1000);
      } else {
        spinner.style.display = 'none';
        errorMsg.textContent = data.error || "Login failed.";
        errorMsg.classList.remove('hidden');
      }
    } catch (error) {
      spinner.style.display = 'none';
      errorMsg.textContent = "Unexpected error occurred.";
      errorMsg.classList.remove('hidden');
      console.error(error);
    }
  });
</script>

</body>
</html>

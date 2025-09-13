<?php
require_once 'config.php';

try {
    // Admin
    $admin_id = 'ADM225';
    $admin_pw = '11200';
    $admin_hash = password_hash($admin_pw, PASSWORD_DEFAULT);
    $stmt1 = $db->prepare("UPDATE employees SET password = ? WHERE employee_id = ?");
    $stmt1->execute([$admin_hash, $admin_id]);

    // Cashier
    $cashier_id = 'CSH225'; 
    $cashier_pw = '22345';
    $cashier_hash = password_hash($cashier_pw, PASSWORD_DEFAULT);
    $stmt2 = $db->prepare("UPDATE employees SET password = ? WHERE employee_id = ?");
    $stmt2->execute([$cashier_hash, $cashier_id]);

    echo "✅ Admin & cashier password successfully updated!";
} catch (PDOException $e) {
    echo "❌ Failed to update password: " . $e->getMessage();
}

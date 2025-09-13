<?php
$plainPassword = '11200';
$storedHash = '$2y$10$8a1PZB6eKqG4bL5F1aYO8OBHqN5Z1rWQOYq1mQ5sWvLZyThF8E6J6';

if (password_verify($plainPassword, $storedHash)) {
    echo "✅ Password matches!";
} else {
    echo "❌ Password does not match!";
}

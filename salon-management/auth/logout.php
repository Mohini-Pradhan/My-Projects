<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$userRole = $_SESSION['user_role'] ?? '';

$_SESSION = [];
session_destroy();

if ($userRole === 'customer') {
    redirect('/customer/login.php');
}

redirect('/auth/login.php');
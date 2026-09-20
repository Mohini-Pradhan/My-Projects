<?php
    declare(strict_types=1);

    require_once __DIR__ . "/../config/app.php";

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }    
    function redirect(string $path):never{
        header('Location: ' . BASE_URL . $path);
        exit;
    }
    function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
    function requireLogin(): void{
        if (!isLoggedIn()) {
            $_SESSION['error_message'] = "Please login first.";
            redirect('/auth/login.php');
        }
    }
    function isAdmin(): bool
    {
        return in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'], true);
    }
    function isCustomer(): bool
    {
        return ($_SESSION['user_role'] ?? '') === 'customer';
    }
    function requireAdmin(): void
    {
        requireLogin();
        if (!isAdmin()) {
            $_SESSION['error_message'] = 'This page is only available to salon administrators.';
            redirect('/customer/index.php');
        }
    }
    function requireCustomer(): void
    {
        requireLogin();
        if (!isCustomer()) {
            redirect('/index.php');
        }
    }
    function e(?string $text): string{
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }

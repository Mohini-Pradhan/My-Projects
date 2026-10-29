<?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    require_once "config/db.php";

    if (!isset($_SESSION["admin_id"])) {
        header("Location: login.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT id, username, display_name, email, photo, theme
        FROM admins
        WHERE id = :id
        LIMIT 1"
    );
    $stmt->execute([":id" => $_SESSION["admin_id"]]);
    $currentAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentAdmin) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
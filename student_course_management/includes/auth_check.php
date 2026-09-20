<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        require_once __DIR__ . "/../config/db.php";
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    }
?>
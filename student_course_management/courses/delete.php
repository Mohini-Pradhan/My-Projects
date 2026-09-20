<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $id = $_GET["id"] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION["success"] = "Course deleted successfully.";
    }
    header("Location: list.php");
    exit;
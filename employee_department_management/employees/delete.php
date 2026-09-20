<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }

    require_once __DIR__ . "/../config/database.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $pdo->prepare(
                "DELETE FROM employees WHERE id = :id"
            );

            $stmt->execute(["id" => $id]);
        }
    }

    header("Location: index.php");
    exit;
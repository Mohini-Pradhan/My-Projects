<?php
    session_start();

    if (empty($_SESSION["user_id"])) {
        header("Location: ../auth/login.php");
        exit;
    }

    require_once __DIR__ . "/../config/database.php";

    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        $check = $pdo->prepare(
            "SELECT COUNT(*) FROM employees WHERE department_id = :id"
        );
        $check->execute(["id" => $id]);

        if ($check->fetchColumn() > 0) {
            header("Location: index.php?error=employees_exist");
            exit;
        }

        $delete = $pdo->prepare(
            "DELETE FROM departments WHERE id = :id"
        );
        $delete->execute(["id" => $id]);
    }

    header("Location: index.php");
    exit;
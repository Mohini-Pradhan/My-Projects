<?php
    declare(strict_types=1);  // login details: email - pradhanmohini049@gmail.com, password - 123456 ||

    $host = "localhost";    
    $username = "root";
    $password = "";
    $dbname = "employee_management_system";

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    } catch (PDOException $error) {
        exit('Database connection failed: ' . $error->getMessage());
    }
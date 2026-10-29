<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "final_assessment";

    try{
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $password
        );

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    } catch (PDOException $e) {
        die("Database connection failed.");
    }

?>
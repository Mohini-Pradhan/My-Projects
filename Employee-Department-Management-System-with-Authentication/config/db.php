<?php
    define("BASE_URL","/my_ws_php_classes/academy-exam/Employee-Department-Management-System-with-Authentication/");
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "employee_management_system";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("MySQLi connection failed: " . mysqli_connect_error());
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}

?>
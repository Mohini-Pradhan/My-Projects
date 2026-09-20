<?php
    define("BASE_URL", "/my_ws_php_classes\Additional-Project-Practice-Scenarios\student_course_management"); // login details: email - pradhanmohini049@gmail.com, password - 123456 ||

    $host ="localhost";
    $username ="root";
    $password ="";
    $dbname ="student_course_management";

    try{
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } 
    catch(PDOException $e){
        die("Database Connection Failed: " . $e->getMessage());
    };

?>
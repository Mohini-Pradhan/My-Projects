<?php
include "../config/db.php";
include "../includes/auth.php";

$department_id = (int) ($_GET["id"] ?? 0);

$check_employee = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM employees
     WHERE department_id = ?"
);

mysqli_stmt_bind_param($check_employee, "i", $department_id);
mysqli_stmt_execute($check_employee);

$result = mysqli_stmt_get_result($check_employee);
$total_employees = mysqli_fetch_assoc($result)["total"];

if ($total_employees > 0) {
    header("Location: index.php?error=has_employees");
    exit();
}

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM departments WHERE id = ?"
);

mysqli_stmt_bind_param($delete, "i", $department_id);

if (mysqli_stmt_execute($delete)) {
    header("Location: index.php?success=deleted");
    exit();
}

header("Location: index.php");
exit();
?>
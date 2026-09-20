<?php
include "../config/db.php";
include "../includes/auth.php";

$employee_id = (int) ($_GET["id"] ?? 0);

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM employees WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($check, "i", $employee_id);
mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM employees WHERE id = ?"
);

mysqli_stmt_bind_param($delete, "i", $employee_id);

if (mysqli_stmt_execute($delete)) {
    header("Location: index.php?success=deleted");
    exit();
}

header("Location: index.php");
exit();
?>
<?php
include "../config/db.php";
include "../includes/auth.php";

$employee_id = (int) ($_GET["id"] ?? 0);

$query = mysqli_prepare(
    $conn,
    "SELECT
        e.*,
        d.department_name,
        d.department_code
     FROM employees e
     INNER JOIN departments d
     ON e.department_id = d.id
     WHERE e.id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($query, "i", $employee_id);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$employee = mysqli_fetch_assoc($result);

if (!$employee) {
    header("Location: index.php");
    exit();
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Employee Details</h2>

                        <a href="index.php" class="btn btn-secondary">
                            Back to List
                        </a>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <strong>Employee ID:</strong><br>
                            <?= $employee["id"] ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Employee Name:</strong><br>
                            <?= htmlspecialchars($employee["employee_name"]) ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($employee["email"]) ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Phone:</strong><br>
                            <?= htmlspecialchars($employee["phone"]) ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Gender:</strong><br>
                            <?= htmlspecialchars($employee["gender"]) ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Department:</strong><br>
                            <?= htmlspecialchars($employee["department_name"]) ?>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($employee["department_code"]) ?>
                            </span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Salary:</strong><br>
                            ₹ <?= number_format($employee["salary"], 2) ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <strong>Joining Date:</strong><br>
                            <?= htmlspecialchars($employee["joining_date"]) ?>
                        </div>

                        <div class="col-md-12 mb-3">
                            <strong>Address:</strong><br>
                            <?= nl2br(htmlspecialchars($employee["address"])) ?>
                        </div>

                        <div class="col-md-12">
                            <strong>Created At:</strong><br>
                            <?= htmlspecialchars($employee["created_at"]) ?>
                        </div>

                    </div>

                    <hr>

                    <a href="edit.php?id=<?= $employee["id"] ?>"
                       class="btn btn-warning">
                        Edit Employee
                    </a>

                    <a href="delete.php?id=<?= $employee["id"] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete this employee?')">
                        Delete Employee
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
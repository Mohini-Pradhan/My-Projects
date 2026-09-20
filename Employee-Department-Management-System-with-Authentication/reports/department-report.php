<?php
include "../config/db.php";
include "../includes/auth.php";

$report = mysqli_query(
    $conn,
    "SELECT
        d.department_name,
        d.department_code,
        COUNT(e.id) AS total_employees
     FROM departments d
     LEFT JOIN employees e
     ON d.id = e.department_id
     GROUP BY d.id, d.department_name, d.department_code
     ORDER BY total_employees DESC"
);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Department Report</h2>

        <a href="salary-report.php" class="btn btn-outline-primary">
            Salary Report
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Department</th>
                        <th>Code</th>
                        <th>Total Employees</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($report) > 0) { ?>

                        <?php $serial = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($report)) { ?>
                            <tr>
                                <td><?= $serial++ ?></td>

                                <td>
                                    <?= htmlspecialchars($row["department_name"]) ?>
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($row["department_code"]) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-success fs-6">
                                        <?= $row["total_employees"] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No department data found.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
<?php
include "../config/db.php";
include "../includes/auth.php";

$salary_report = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT
            COALESCE(MAX(salary), 0) AS highest_salary,
            COALESCE(MIN(salary), 0) AS lowest_salary,
            COALESCE(AVG(salary), 0) AS average_salary,
            COALESCE(SUM(salary), 0) AS total_salary
         FROM employees"
    )
);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Salary Report</h2>

        <a href="department-report.php" class="btn btn-outline-primary">
            Department Report
        </a>
    </div>

    <div class="row g-4">

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h6 class="text-muted">Highest Salary</h6>

                    <h3 class="text-success">
                        ₹ <?= number_format($salary_report["highest_salary"], 2) ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h6 class="text-muted">Lowest Salary</h6>

                    <h3 class="text-danger">
                        ₹ <?= number_format($salary_report["lowest_salary"], 2) ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Average Salary</h6>

                    <h3 class="text-primary">
                        ₹ <?= number_format($salary_report["average_salary"], 2) ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-dark">
                <div class="card-body">
                    <h6 class="text-muted">Total Salary Expense</h6>

                    <h3 class="text-dark">
                        ₹ <?= number_format($salary_report["total_salary"], 2) ?>
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">

            <h5>Salary Summary</h5>

            <p class="text-muted mb-0">This report calculates company salary</p>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
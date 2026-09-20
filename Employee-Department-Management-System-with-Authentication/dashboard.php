<?php
include "config/db.php";
include "includes/auth.php";

$total_departments = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM departments")
)["total"];

$salary_stats = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT
            COUNT(*) AS total_employees,
            COALESCE(MAX(salary), 0) AS highest_salary,
            COALESCE(MIN(salary), 0) AS lowest_salary,
            COALESCE(AVG(salary), 0) AS average_salary,
            COALESCE(SUM(salary), 0) AS total_salary
         FROM employees"
    )
);

$recent_employees = mysqli_query(
    $conn,
    "SELECT e.employee_name, e.email, e.salary, d.department_name
     FROM employees e
     INNER JOIN departments d
     ON e.department_id = d.id
     ORDER BY e.id DESC
     LIMIT 5"
);
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container py-4">

    <h2>Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?></h2>
    <p class="text-muted">Employee Department Management Dashboard</p>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6>Total Employees</h6>
                    <h2><?= $salary_stats["total_employees"] ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6>Total Departments</h6>
                    <h2><?= $total_departments ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white shadow-sm">
                <div class="card-body">
                    <h6>Total Salary Expense</h6>
                    <h2>₹ <?= number_format($salary_stats["total_salary"], 2) ?></h2>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Highest Salary</h6>
                    <h4 class="text-success">
                        ₹ <?= number_format($salary_stats["highest_salary"], 2) ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Lowest Salary</h6>
                    <h4 class="text-danger">
                        ₹ <?= number_format($salary_stats["lowest_salary"], 2) ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Average Salary</h6>
                    <h4 class="text-primary">
                        ₹ <?= number_format($salary_stats["average_salary"], 2) ?>
                    </h4>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-between mb-3">
        <h4>Recently Added Employees</h4>

        <a href="employee/create.php" class="btn btn-success">
            + Add Employee
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Salary</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($recent_employees) > 0) { ?>

                        <?php while ($employee = mysqli_fetch_assoc($recent_employees)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($employee["employee_name"]) ?></td>
                                <td><?= htmlspecialchars($employee["email"]) ?></td>
                                <td><?= htmlspecialchars($employee["department_name"]) ?></td>
                                <td>₹ <?= number_format($employee["salary"], 2) ?></td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No employees added yet.
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>
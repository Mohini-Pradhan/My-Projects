<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$stats = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM departments) AS total_departments,
        COUNT(*) AS total_employees,
        MAX(salary) AS highest_salary,
        MIN(salary) AS lowest_salary,
        AVG(salary) AS average_salary,
        SUM(salary) AS total_salary_expense
     FROM employees"
)->fetch();

$latestEmployees = $pdo->query(
    "SELECT
        employees.employee_name,
        employees.email,
        employees.salary,
        employees.joining_date,
        departments.department_name
     FROM employees
     INNER JOIN departments ON employees.department_id = departments.id
     ORDER BY employees.joining_date DESC, employees.id DESC
     LIMIT 5"
)->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-dark bg-gradient text-white rounded-5 p-4 p-md-5 mb-4 shadow-lg">
    <div class="row align-items-center">
        <div class="col-md-8">
            <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-3">🏢 ADMIN DASHBOARD</span>
            <h1 class="display-6 fw-bold">
                Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>
            </h1>
            <p class="mb-0 opacity-75">
                Manage departments, employees, reports, and your account from one place.
            </p>
        </div>

        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="employees/create.php" class="btn btn-light rounded-pill me-2 px-4">
                + Add Employee
            </a>

            <a href="departments/create.php" class="btn btn-outline-light rounded-pill px-4">
                + Add Department
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 border-start border-primary border-4 shadow-sm rounded-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-2">Total Employees</p>
                    <h2 class="h3 fw-bold text-primary mb-0">
                        <?= $stats['total_employees'] ?>
                    </h2>
                </div>
                <span class="fs-2">👥</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 border-start border-success border-4 shadow-sm rounded-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-2">Total Departments</p>
                    <h2 class="h3 fw-bold text-success mb-0">
                        <?= $stats['total_departments'] ?>
                    </h2>
                </div>
                <span class="fs-2">🏬</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 border-start border-warning border-4 shadow-sm rounded-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-2">Average Salary</p>
                    <h2 class="h4 fw-bold text-warning mb-0">
                        ₹<?= number_format($stats['average_salary'] ?? 0, 2) ?>
                    </h2>
                </div>
                <span class="fs-2">📊</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card h-100 border-0 border-start border-danger border-4 shadow-sm rounded-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-2">Total Salary Expense</p>
                    <h2 class="h4 fw-bold text-danger mb-0">
                        ₹<?= number_format($stats['total_salary_expense'] ?? 0, 2) ?>
                    </h2>
                </div>
                <span class="fs-2">💰</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 rounded-top-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">👋 Recently Joined Employees</h2>

                    <a href="employees/index.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        View All
                    </a>
                </div>
            </div>

            <div class="card-body px-4">
                <?php if ($latestEmployees): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted">
                                    <th class="border-0">Name</th>
                                    <th class="border-0">Department</th>
                                    <th class="border-0">Salary</th>
                                    <th class="border-0">Joining Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($latestEmployees as $employee): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="bg-dark bg-opacity-10 text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:40px; height:40px;">
                                                    <?= strtoupper(substr($employee['employee_name'], 0, 1)) ?>
                                                </span>
                                                <div>
                                                    <strong><?= htmlspecialchars($employee['employee_name']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($employee['email']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border rounded-pill px-3"><?= htmlspecialchars($employee['department_name']) ?></span></td>
                                        <td class="fw-semibold">₹<?= number_format($employee['salary'], 2) ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($employee['joining_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No employees have been added yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 rounded-top-4">
                <h2 class="h5 mb-0">📈 Salary Overview</h2>
            </div>

            <div class="card-body px-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <p class="text-muted mb-1">Highest Salary</p>
                        <h3 class="h4 text-success mb-0">
                            ₹<?= number_format($stats['highest_salary'] ?? 0, 2) ?>
                        </h3>
                    </div>
                    <span class="fs-3">🚀</span>
                </div>

                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Lowest Salary</p>
                        <h3 class="h4 text-danger mb-0">
                            ₹<?= number_format($stats['lowest_salary'] ?? 0, 2) ?>
                        </h3>
                    </div>
                    <span class="fs-3">📉</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
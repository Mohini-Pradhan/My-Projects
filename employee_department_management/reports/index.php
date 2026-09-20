<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $salaryReport = $pdo->query(
        'SELECT
            MAX(salary) AS highest_salary,
            MIN(salary) AS lowest_salary,
            AVG(salary) AS average_salary,
            SUM(salary) AS total_salary
        FROM employees'
    )->fetch();

    $departmentReport = $pdo->query(
        'SELECT
            departments.department_name,
            departments.department_code,
            COUNT(employees.id) AS total_employees,
            COALESCE(SUM(employees.salary), 0) AS total_salary,
            COALESCE(AVG(employees.salary), 0) AS average_salary
        FROM departments
        LEFT JOIN employees
            ON employees.department_id = departments.id
        GROUP BY
            departments.id,
            departments.department_name,
            departments.department_code
        ORDER BY departments.department_name'
    )->fetchAll();

    $pageTitle = 'Reports';
    require_once __DIR__ . '/../includes/header.php';
?>

<style>
    @media print {
        nav, footer, .no-print {
            display: none !important;
        }

        main.container {
            max-width: 100% !important;
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-dark rounded-pill px-3 py-2 mb-2">📊 REPORTS</span>
        <h1 class="h3 fw-bold mb-1">Reports</h1>
        <p class="text-muted mb-0">
            Department and employee salary reports.
        </p>
    </div>

    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 no-print">
        🖨️ Print Report
    </button>
</div>

<h2 class="h5 fw-bold mb-3">Salary Report</h2>

<div class="row g-4 mb-5">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 border-start border-success border-4 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Highest Salary</p>
                    <h3 class="h4 text-success mb-0">
                        ₹<?= number_format($salaryReport['highest_salary'] ?? 0, 2) ?>
                    </h3>
                </div>
                <span class="fs-3">🚀</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 border-start border-danger border-4 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Lowest Salary</p>
                    <h3 class="h4 text-danger mb-0">
                        ₹<?= number_format($salaryReport['lowest_salary'] ?? 0, 2) ?>
                    </h3>
                </div>
                <span class="fs-3">📉</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 border-start border-primary border-4 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Average Salary</p>
                    <h3 class="h4 text-primary mb-0">
                        ₹<?= number_format($salaryReport['average_salary'] ?? 0, 2) ?>
                    </h3>
                </div>
                <span class="fs-3">📊</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 border-start border-warning border-4 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Salary Expense</p>
                    <h3 class="h4 text-warning mb-0">
                        ₹<?= number_format($salaryReport['total_salary'] ?? 0, 2) ?>
                    </h3>
                </div>
                <span class="fs-3">💰</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 rounded-top-4">
        <h2 class="h5 fw-bold mb-0">🏬 Department-wise Employee Report</h2>
    </div>

    <div class="card-body px-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted">
                        <th class="border-0">Department</th>
                        <th class="border-0">Code</th>
                        <th class="border-0">Total Employees</th>
                        <th class="border-0">Average Salary</th>
                        <th class="border-0 text-end">Total Salary</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($departmentReport as $department): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($department['department_name']) ?></td>
                            <td><span class="badge bg-light text-dark border rounded-pill px-3"><?= htmlspecialchars($department['department_code']) ?></span></td>
                            <td><?= $department['total_employees'] ?></td>
                            <td>₹<?= number_format($department['average_salary'], 2) ?></td>
                            <td class="text-end fw-semibold">
                                ₹<?= number_format($department['total_salary'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
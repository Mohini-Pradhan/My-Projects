<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";
    require_once "../includes/header.php";

    $courseStats = $pdo->query("
        SELECT c.course_name, c.course_code, COUNT(e.id) AS total_enrolled
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        GROUP BY c.id, c.course_name, c.course_code
        ORDER BY total_enrolled DESC
    ")->fetchAll();

    $feeStats = $pdo->query("
        SELECT 
            MAX(fees) AS max_fee,
            MIN(fees) AS min_fee,
            AVG(fees) AS avg_fee,
            SUM(fees) AS total_fee
        FROM courses
    ")->fetch();

    $revenueStats = $pdo->query("
        SELECT SUM(c.fees) AS total_revenue, COUNT(e.id) AS total_enrollments
        FROM enrollments e
        INNER JOIN courses c ON e.course_id = c.id
    ")->fetch();
?>

    <h2 class="mb-4">Reports</h2>

    <h4 class="mb-3">Course Fee Statistics</h4>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h6 class="card-title">Highest Fee</h6>
                    <p class="fs-4 mb-0"><?= $feeStats["max_fee"] !== null ? number_format($feeStats["max_fee"], 2) : "—" ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger shadow">
                <div class="card-body">
                    <h6 class="card-title">Lowest Fee</h6>
                    <p class="fs-4 mb-0"><?= $feeStats["min_fee"] !== null ? number_format($feeStats["min_fee"], 2) : "—" ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <h6 class="card-title">Average Fee</h6>
                    <p class="fs-4 mb-0"><?= $feeStats["avg_fee"] !== null ? number_format($feeStats["avg_fee"], 2) : "—" ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary shadow">
                <div class="card-body">
                    <h6 class="card-title">Total (all courses)</h6>
                    <p class="fs-4 mb-0"><?= $feeStats["total_fee"] !== null ? number_format($feeStats["total_fee"], 2) : "—" ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Enrollment Revenue</h4>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title">Total Enrollments</h6>
                    <p class="fs-4 mb-0"><?= $revenueStats["total_enrollments"] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="card-title">Total Revenue Collected</h6>
                    <p class="fs-4 mb-0"><?= $revenueStats["total_revenue"] !== null ? number_format($revenueStats["total_revenue"], 2) : "0.00" ?></p>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Course-wise Enrollment Count</h4>
    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Total Enrolled</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($courseStats): ?>
                <?php foreach ($courseStats as $i => $cs): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($cs["course_name"]) ?></td>
                        <td><?= htmlspecialchars($cs["course_code"]) ?></td>
                        <td><?= $cs["total_enrolled"] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted">No courses found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php require_once "../includes/footer.php"; ?>
<?php
    require_once "includes/auth_check.php";
    require_once "config/db.php";
    require_once "includes/header.php";

    $totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $totalEnrollments = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();

    $latestStudent = $pdo->query("SELECT student_name, created_at FROM students ORDER BY id DESC LIMIT 1")->fetch();
    $latestCourse = $pdo->query("SELECT course_name, created_at FROM courses ORDER BY id DESC LIMIT 1")->fetch();

    $chartData = $pdo->query("
        SELECT c.course_name, COUNT(e.id) AS total
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        GROUP BY c.id, c.course_name
        ORDER BY total DESC
        LIMIT 8
    ")->fetchAll();

    $chartLabels = array_column($chartData, "course_name");
    $chartValues = array_column($chartData, "total");

    $recentActivity = $pdo->query("
        SELECT s.student_name, c.course_name, e.enrollment_date
        FROM enrollments e
        INNER JOIN students s ON e.student_id = s.id
        INNER JOIN courses c ON e.course_id = c.id
        ORDER BY e.id DESC
        LIMIT 5
    ")->fetchAll();
    ?>

    <h2 class="mb-4 text-center">Admin Dashboard</h2>

    <div class="row g-3 mb-2">
        <div class="col-md-4">
            <div class="card stat-card text-white bg-primary shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity:0.8;">Total Students</h6>
                        <p class="stat-number mb-0" data-count="<?= $totalStudents ?>">0</p>
                    </div>
                    <div class="stat-icon">👨‍🎓</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card text-white bg-success shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity:0.8;">Total Courses</h6>
                        <p class="stat-number mb-0" data-count="<?= $totalCourses ?>">0</p>
                    </div>
                    <div class="stat-icon">📚</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card text-white bg-info shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1" style="opacity:0.8;">Total Enrollments</h6>
                        <p class="stat-number mb-0" data-count="<?= $totalEnrollments ?>">0</p>
                    </div>
                    <div class="stat-icon">📝</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 mt-1">
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/students/add.php" class="quick-action bg-primary">+ Add Student</a>
        </div>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/courses/add.php" class="quick-action bg-success">+ Add Course</a>
        </div>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/enrollments/add.php" class="quick-action bg-info">+ Enroll Student</a>
        </div>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/reports/index.php" class="quick-action bg-secondary">View Reports</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Enrollments by Course</h5>
                    <?php if (!empty($chartValues) && array_sum($chartValues) > 0): ?>
                        <canvas id="enrollmentChart" height="220"
                            data-labels="<?= htmlspecialchars(json_encode($chartLabels), ENT_QUOTES) ?>"
                            data-values="<?= htmlspecialchars(json_encode($chartValues), ENT_QUOTES) ?>"></canvas>
                    <?php else: ?>
                        <p class="text-muted mb-0">No enrollment data yet — enroll a student to see this chart.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Recent Enrollments</h5>
                    <?php if ($recentActivity): ?>
                        <?php foreach ($recentActivity as $a): ?>
                            <div class="activity-item">
                                <strong><?= htmlspecialchars($a["student_name"]) ?></strong>
                                enrolled in <strong><?= htmlspecialchars($a["course_name"]) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($a["enrollment_date"]) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No recent activity yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Latest Student</h6>
                    <?php if ($latestStudent): ?>
                        <p class="mb-0"><?= htmlspecialchars($latestStudent["student_name"]) ?></p>
                        <small class="text-muted"><?= $latestStudent["created_at"] ?></small>
                    <?php else: ?>
                        <p class="text-muted mb-0">No students yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Latest Course</h6>
                    <?php if ($latestCourse): ?>
                        <p class="mb-0"><?= htmlspecialchars($latestCourse["course_name"]) ?></p>
                        <small class="text-muted"><?= $latestCourse["created_at"] ?></small>
                    <?php else: ?>
                        <p class="text-muted mb-0">No courses yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>

<?php require_once "includes/footer.php"; ?>
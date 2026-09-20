<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";
    require_once "../includes/header.php";

    $sql = "SELECT e.id, s.student_name, c.course_name, c.duration, c.fees, e.enrollment_date
            FROM enrollments e
            INNER JOIN students s ON e.student_id = s.id
            INNER JOIN courses c ON e.course_id = c.id
            ORDER BY e.id DESC";
    $enrollments = $pdo->query($sql)->fetchAll();
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Enrollments</h2>
        <a href="add.php" class="btn btn-primary">+ Enroll Student</a>
    </div>

    <?php if (isset($_SESSION["success"])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION["success"]); unset($_SESSION["success"]); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION["error"])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION["error"]); unset($_SESSION["error"]); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Course Name</th>
                <th>Duration</th>
                <th>Fees</th>
                <th>Enrollment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($enrollments): ?>
                <?php foreach ($enrollments as $i => $e): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($e["student_name"]) ?></td>
                        <td><?= htmlspecialchars($e["course_name"]) ?></td>
                        <td><?= htmlspecialchars($e["duration"]) ?></td>
                        <td><?= number_format($e["fees"], 2) ?></td>
                        <td><?= htmlspecialchars($e["enrollment_date"]) ?></td>
                        <td>
                            <a href="delete.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this enrollment?');">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No enrollments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php require_once "../includes/footer.php"; ?>
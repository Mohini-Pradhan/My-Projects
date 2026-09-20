<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";
    require_once "../includes/header.php";

    $courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Courses</h2>
        <a href="add.php" class="btn btn-primary">+ Add Course</a>
    </div>

    <?php if (isset($_SESSION["success"])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION["success"]); unset($_SESSION["success"]); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Duration (months)</th>
                <th>Fees</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($courses): ?>
                <?php foreach ($courses as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($c["course_name"]) ?></td>
                        <td><?= htmlspecialchars($c["course_code"]) ?></td>
                        <td><?= htmlspecialchars($c["duration"]) ?></td>
                        <td><?= number_format($c["fees"], 2) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No courses found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php require_once "../includes/footer.php"; ?>
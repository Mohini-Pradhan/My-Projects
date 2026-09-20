<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $errors = [];

    $students = $pdo->query("SELECT id, student_name FROM students ORDER BY student_name")->fetchAll();
    $courses = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name")->fetchAll();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $student_id = $_POST["student_id"] ?? "";
        $course_id = $_POST["course_id"] ?? "";
        $enrollment_date = $_POST["enrollment_date"] ?? "";

        if (empty($student_id)) $errors[] = "Please select a student.";
        if (empty($course_id)) $errors[] = "Please select a course.";
        if (empty($enrollment_date)) $errors[] = "Enrollment date is required.";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
            $stmt->execute([$student_id, $course_id]);
            if ($stmt->fetch()) {
                $errors[] = "This student is already enrolled in this course.";
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, enrollment_date) VALUES (?, ?, ?)");
            $stmt->execute([$student_id, $course_id, $enrollment_date]);

            $_SESSION["success"] = "Student enrolled successfully.";
            header("Location: list.php");
            exit;
        }
    }

    require_once "../includes/header.php";
    ?>

    <h2 class="mb-4">Enroll Student</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($students) || empty($courses)): ?>
        <div class="alert alert-warning">
            You need at least one student and one course before you can create an enrollment.
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 shadow-sm rounded" style="max-width: 600px;">
        <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select" required>
                <option value="">-- Select Student --</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (isset($_POST["student_id"]) && $_POST["student_id"] == $s["id"]) ? "selected" : "" ?>>
                        <?= htmlspecialchars($s['student_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Course</label>
            <select name="course_id" class="form-select" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($_POST["course_id"]) && $_POST["course_id"] == $c["id"]) ? "selected" : "" ?>>
                        <?= htmlspecialchars($c["course_name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Enrollment Date</label>
            <input type="date" name="enrollment_date" class="form-control" value="<?= htmlspecialchars($_POST['enrollment_date'] ?? date('Y-m-d')) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Enroll</button>
        <a href="list.php" class="btn btn-secondary">Cancel</a>
    </form>

<?php require_once "../includes/footer.php"; ?>
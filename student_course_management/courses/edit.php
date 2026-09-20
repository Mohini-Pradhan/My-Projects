<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $id = $_GET["id"] ?? null;
    if (!$id) { header("Location: list.php"); exit; }

    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch();

    if (!$course) { header("Location: list.php"); exit; }

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["course_name"]);
        $code = trim($_POST["course_code"]);
        $duration = $_POST["duration"];
        $fees = $_POST["fees"];
        $description = trim($_POST["description"]);

        if (empty($name)) $errors[] = "Course name is required.";
        if (empty($code)) $errors[] = "Course code is required.";
        if (!is_numeric($duration) || $duration <= 0) $errors[] = "Duration must be greater than zero.";
        if (!is_numeric($fees) || $fees <= 0) $errors[] = "Fees must be greater than zero.";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_code = ? AND id != ?");
            $stmt->execute([$code, $id]);
            if ($stmt->fetch()) {
                $errors[] = "Course code already used by another course.";
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE courses SET course_name=?, course_code=?, duration=?, fees=?, description=? WHERE id=?");
            $stmt->execute([$name, $code, $duration, $fees, $description, $id]);

            $_SESSION["success"] = "Course updated successfully.";
            header("Location: list.php");
            exit;
        } else {
            $course = array_merge($course, $_POST);
        }
    }

    require_once "../includes/header.php";
?>

    <h2 class="mb-4">Edit Course</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 shadow-sm rounded" style="max-width: 600px;">
        <div class="mb-3">
            <label class="form-label">Course Name</label>
            <input type="text" name="course_name" class="form-control" value="<?= htmlspecialchars($course['course_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Course Code</label>
            <input type="text" name="course_code" class="form-control" value="<?= htmlspecialchars($course['course_code']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Duration (months)</label>
            <input type="number" name="duration" class="form-control" value="<?= htmlspecialchars($course['duration']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Fees</label>
            <input type="number" step="0.01" name="fees" class="form-control" value="<?= htmlspecialchars($course['fees']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($course['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Course</button>
        <a href="list.php" class="btn btn-secondary">Cancel</a>
    </form>

<?php require_once "../includes/footer.php"; ?>
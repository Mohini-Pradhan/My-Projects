<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

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
            $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_code = ?");
            $stmt->execute([$code]);
            if ($stmt->fetch()) {
                $errors[] = "Course code already exists.";
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, course_code, duration, fees, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $code, $duration, $fees, $description]);

            $_SESSION["success"] = "Course added successfully.";
            header("Location: list.php");
            exit;
        }
    }

    require_once '../includes/header.php';
    ?>

    <h2 class="mb-4">Add Course</h2>

    <div class="row">
        <div class="col-md-7">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="bg-white p-4 shadow-sm rounded">
                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" value="<?= htmlspecialchars($_POST['course_name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Course Code</label>
                    <input type="text" name="course_code" class="form-control" value="<?= htmlspecialchars($_POST['course_code'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Duration (months)</label>
                    <input type="number" name="duration" class="form-control" value="<?= htmlspecialchars($_POST['duration'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fees</label>
                    <input type="number" step="0.01" name="fees" class="form-control" value="<?= htmlspecialchars($_POST['fees'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Course</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>

            <div class="col-md-5 d-flex align-items-center justify-content-center">
                <img src="<?= BASE_URL ?>/includes/logo.png" alt="Institute Logo" style="max-width: 550px; opacity: 0.4;">
            </div>
    </div>

<?php require_once '../includes/footer.php'; ?>
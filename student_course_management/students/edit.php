<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $id = $_GET['id'] ?? null;
    if (!$id) { header("Location: list.php"); exit; }

    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();

    if (!$student) { header("Location: list.php"); exit; }

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["student_name"]);
        $email = trim($_POST["email"]);
        $phone = trim($_POST["phone"]);
        $gender = $_POST["gender"] ?? "";
        $dob = $_POST["dob"];
        $address = trim($_POST["address"]);

        if (empty($name)) $errors[] = "Student name is required.";
        if (empty($email)) $errors[] = "Email is required.";
        if (!empty($phone) && !ctype_digit($phone)) $errors[] = "Phone must contain only digits.";
        if (empty($dob)) $errors[] = "Date of birth is required.";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $errors[] = "Email already used by another student.";
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE students SET student_name=?, email=?, phone=?, gender=?, dob=?, address=? WHERE id=?");
            $stmt->execute([$name, $email, $phone, $gender, $dob, $address, $id]);

            $_SESSION['success'] = "Student updated successfully.";
            header("Location: list.php");
            exit;
        } else {
            $student = array_merge($student, $_POST);
        }
    }

    require_once "../includes/header.php";
?>

    <h2 class="mb-4">Edit Student</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

    <form method="POST" class="bg-white p-4 shadow-sm rounded" style="max-width: 600px;">
        <div class="mb-3">
            <label class="form-label">Student Name</label>
            <input type="text" name="student_name" class="form-control" value="<?= htmlspecialchars($student['student_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label d-block">Gender</label>
            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="<?= $g ?>" <?= $student['gender'] === $g ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= $g ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($student['dob']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($student['address']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
        <a href="list.php" class="btn btn-secondary">Cancel</a>
    </form>

<?php require_once "../includes/footer.php"; ?>
<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

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
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Email already exists.";
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO students (student_name, email, phone, gender, dob, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $gender, $dob, $address]);

                $_SESSION["success"] = "Student added successfully.";
                header("Location: list.php");
                exit;
            }
        }

        require_once "../includes/header.php";
        ?>

        <h2 class="mb-4">Add Student</h2>
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
                        <label class="form-label">Student Name</label>
                        <input type="text" name="student_name" class="form-control" value="<?= htmlspecialchars($_POST['student_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Gender</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="Male" required>
                            <label class="form-check-label">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="Female">
                            <label class="form-check-label">Female</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="Other">
                            <label class="form-check-label">Other</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
            <div class="col-md-5 d-flex align-items-center justify-content-center">
                <img src="<?= BASE_URL ?>/includes/logo.png" alt="Institute Logo" style="max-width: 550px; opacity: 0.4;">
            </div>
        </div>

<?php require_once "../includes/footer.php"; ?>
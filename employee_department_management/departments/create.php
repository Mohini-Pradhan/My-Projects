<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $name = '';
    $code = '';
    $description = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['department_name'] ?? '');
        $code = strtoupper(trim($_POST['department_code'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $code === '') {
            $error = 'Department name and department code are required.';
        } else {
            $check = $pdo->prepare(
                'SELECT id FROM departments WHERE department_code = :code'
            );
            $check->execute(['code' => $code]);

            if ($check->fetch()) {
                $error = 'This department code already exists.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO departments (department_name, department_code, description)
                    VALUES (:name, :code, :description)'
                );

                $stmt->execute([
                    'name' => $name,
                    'code' => $code,
                    'description' => $description
                ]);

                header('Location: index.php');
                exit;
            }
        }
    }
?>

<?php

    $pageTitle = "Add Department";
    require_once __DIR__ . "/../includes/header.php";
?>

        <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 700px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; font-size:20px;">
                        🏬
                    </span>
                    <div>
                        <h1 class="h3 fw-bold mb-0">Add Department</h1>
                        <p class="text-muted mb-0">Create a new department record</p>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>⚠️ <?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="text" name="department_name" id="department_name" class="form-control"
                               placeholder="Department Name" value="<?= htmlspecialchars($name) ?>" required>
                        <label for="department_name">Department Name *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="department_code" id="department_code" class="form-control"
                               placeholder="Example: HR or IT" value="<?= htmlspecialchars($code) ?>" required>
                        <label for="department_code">Department Code *</label>
                    </div>

                    <div class="form-floating mb-4">
                        <textarea name="description" id="description" class="form-control" placeholder="Description" style="height: 120px" rows="4"><?= htmlspecialchars($description) ?></textarea>
                        <label for="description">Description</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill px-4">
                            Save Department
                        </button>

                        <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
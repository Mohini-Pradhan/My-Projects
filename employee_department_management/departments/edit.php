<?php
    session_start();

    if (empty($_SESSION["user_id"])) {
        header("Location: ../auth/login.php");
        exit;
    }

    require_once __DIR__ . "/../config/database.php";

    $id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM departments WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $department = $stmt->fetch();

    if (!$department) {
        header("Location: index.php");
        exit;
    }

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["department_name"] ?? "");
        $code = strtoupper(trim($_POST["department_code"] ?? ""));
        $description = trim($_POST["description"] ?? "");

        if ($name === "" || $code === "") {
            $error = "Department name and code are required.";
        } else {
            $check = $pdo->prepare(
                "SELECT id FROM departments
                WHERE department_code = :code AND id != :id"
            );
            $check->execute(["code" => $code, "id" => $id]);

            if ($check->fetch()) {
                $error = 'This department code already exists.';
            } else {
                $update = $pdo->prepare(
                    "UPDATE departments SET
                        department_name = :name,
                        department_code = :code,
                        description = :description
                    WHERE id = :id"
                );

                $update->execute([
                    "name" => $name,
                    "code" => $code,
                    "description" => $description ?: null,
                    "id" => $id
                ]);

                header("Location: index.php");
                exit;
            }
        }

        $department["department_name"] = $name;
        $department["department_code"] = $code;
        $department["description"] = $description;
    }

    $pageTitle = "Edit Department";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 700px;">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center mb-4">
            <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; font-size:20px;">
                🏬
            </span>
            <div>
                <h1 class="h3 fw-bold mb-0">Edit Department</h1>
                <p class="text-muted mb-0">Update the department details below</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <div>⚠️ <?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?= $department['id'] ?>">

            <div class="form-floating mb-3">
                <input type="text" name="department_name" id="department_name" class="form-control"
                       placeholder="Department Name" value="<?= htmlspecialchars($department['department_name']) ?>" required>
                <label for="department_name">Department Name *</label>
            </div>

            <div class="form-floating mb-3">
                <input type="text" name="department_code" id="department_code" class="form-control"
                       placeholder="Department Code" value="<?= htmlspecialchars($department['department_code']) ?>" required>
                <label for="department_code">Department Code *</label>
            </div>

            <div class="form-floating mb-4">
                <textarea name="description" id="description" class="form-control" placeholder="Description" style="height: 120px" rows="4"><?= htmlspecialchars($department["description"] ?? "") ?></textarea>
                <label for="description">Description</label>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-dark btn-lg rounded-pill px-4">Update Department</button>
                <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
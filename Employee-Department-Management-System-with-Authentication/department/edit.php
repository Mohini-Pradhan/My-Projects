<?php
include "../config/db.php";
include "../includes/auth.php";

$department_id = (int) ($_GET["id"] ?? 0);
$errors = [];

$get_department = mysqli_prepare(
    $conn,
    "SELECT * FROM departments WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($get_department, "i", $department_id);
mysqli_stmt_execute($get_department);

$result = mysqli_stmt_get_result($get_department);
$department = mysqli_fetch_assoc($result);

if (!$department) {
    header("Location: index.php");
    exit();
}

if (isset($_POST["update_department"])) {

    $department_name = trim($_POST["department_name"]);
    $department_code = trim($_POST["department_code"]);
    $description = trim($_POST["description"]);

    if (empty($department_name)) {
        $errors[] = "Department name is required.";
    }

    if (empty($department_code)) {
        $errors[] = "Department code is required.";
    }

    if (empty($errors)) {

        $check = mysqli_prepare(
            $conn,
            "SELECT id
             FROM departments
             WHERE (department_name = ? OR department_code = ?)
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "ssi",
            $department_name,
            $department_code,
            $department_id
        );

        mysqli_stmt_execute($check);

        $check_result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Department name or code already exists.";
        }
    }

    if (empty($errors)) {

        $update = mysqli_prepare(
            $conn,
            "UPDATE departments
             SET department_name = ?,
                 department_code = ?,
                 description = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssi",
            $department_name,
            $department_code,
            $description,
            $department_id
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            $errors[] = "Department update failed.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h2 class="mb-4">Edit Department</h2>

                    <?php if (!empty($errors)) { ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error) { ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Department Name</label>

                            <input type="text"
                                   name="department_name"
                                   class="form-control"
                                   value="<?= htmlspecialchars($_POST["department_name"] ?? $department["department_name"]) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Department Code</label>

                            <input type="text"
                                   name="department_code"
                                   class="form-control"
                                   value="<?= htmlspecialchars($_POST["department_code"] ?? $department["department_code"]) ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Description</label>

                            <textarea name="description"
                                      class="form-control"
                                      rows="4"><?= htmlspecialchars($_POST["description"] ?? $department["description"]) ?></textarea>
                        </div>

                        <button type="submit"
                                name="update_department"
                                class="btn btn-warning">
                            Update Department
                        </button>

                        <a href="index.php" class="btn btn-secondary">
                            Cancel
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
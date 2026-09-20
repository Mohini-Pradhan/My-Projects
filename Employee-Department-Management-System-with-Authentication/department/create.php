<?php
include "../config/db.php";
include "../includes/auth.php";

$errors = [];

if (isset($_POST["add_department"])) {

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
             WHERE department_name = ? OR department_code = ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "ss",
            $department_name,
            $department_code
        );

        mysqli_stmt_execute($check);

        $result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Department name or code already exists.";
        }
    }

    if (empty($errors)) {

        $insert = mysqli_prepare(
            $conn,
            "INSERT INTO departments
             (department_name, department_code, description)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insert,
            "sss",
            $department_name,
            $department_code,
            $description
        );

        if (mysqli_stmt_execute($insert)) {
            header("Location: index.php?success=created");
            exit();
        } else {
            $errors[] = "Department could not be saved.";
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

                    <h2 class="mb-4">Add Department</h2>

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

                            <input type="text" name="department_name" class="form-control" value="<?= htmlspecialchars($_POST["department_name"] ?? "") ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Department Code</label>

                            <input type="text" name="department_code" class="form-control" value="<?= htmlspecialchars($_POST["department_code"] ?? "") ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Description</label>

                            <textarea name="description" class="form-control" rows="4"
                                placeholder="Write department description"><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>
                        </div>

                        <button type="submit" name="add_department" class="btn btn-primary">
                            Save Department
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
<?php
include "../config/db.php";
include "../includes/auth.php";

$employee_id = (int) ($_GET["id"] ?? 0);
$errors = [];

$get_employee = mysqli_prepare(
    $conn,
    "SELECT * FROM employees WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($get_employee, "i", $employee_id);
mysqli_stmt_execute($get_employee);

$result = mysqli_stmt_get_result($get_employee);
$employee = mysqli_fetch_assoc($result);

if (!$employee) {
    header("Location: index.php");
    exit();
}

if (isset($_POST["update_employee"])) {

    $department_id = (int) $_POST["department_id"];
    $employee_name = trim($_POST["employee_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $gender = $_POST["gender"] ?? "";
    $salary = trim($_POST["salary"]);
    $joining_date = $_POST["joining_date"];
    $address = trim($_POST["address"]);

    if (empty($employee_name)) {
        $errors[] = "Employee name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email.";
    }

    if (!preg_match("/^[0-9]+$/", $phone)) {
        $errors[] = "Phone must contain only numbers.";
    }

    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }

    if (!is_numeric($salary) || $salary <= 0) {
        $errors[] = "Salary must be greater than zero.";
    }

    if (empty($joining_date)) {
        $errors[] = "Joining date is required.";
    }

    if ($department_id <= 0) {
        $errors[] = "Please select a department.";
    }

    /* অন্য employee-এর একই email আছে কি না */
    if (empty($errors)) {

        $check_email = mysqli_prepare(
            $conn,
            "SELECT id
             FROM employees
             WHERE email = ? AND id != ?"
        );

        mysqli_stmt_bind_param(
            $check_email,
            "si",
            $email,
            $employee_id
        );

        mysqli_stmt_execute($check_email);

        $email_result = mysqli_stmt_get_result($check_email);

        if (mysqli_num_rows($email_result) > 0) {
            $errors[] = "This email already exists.";
        }
    }

    if (empty($errors)) {

        $update = mysqli_prepare(
            $conn,
            "UPDATE employees
             SET department_id = ?,
                 employee_name = ?,
                 email = ?,
                 phone = ?,
                 gender = ?,
                 salary = ?,
                 joining_date = ?,
                 address = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "issssdssi",
            $department_id,
            $employee_name,
            $email,
            $phone,
            $gender,
            $salary,
            $joining_date,
            $address,
            $employee_id
        );

        if (mysqli_stmt_execute($update)) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            $errors[] = "Employee update failed.";
        }
    }
}

$departments = mysqli_query(
    $conn,
    "SELECT id, department_name
     FROM departments
     ORDER BY department_name ASC"
);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h2 class="mb-4">Edit Employee</h2>

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
                            <label class="form-label">Employee Name</label>

                            <input type="text"
                                   name="employee_name"
                                   class="form-control"
                                   value="<?= htmlspecialchars($_POST["employee_name"] ?? $employee["employee_name"]) ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>

                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="<?= htmlspecialchars($_POST["email"] ?? $employee["email"]) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>

                                <input type="text"
                                       name="phone"
                                       class="form-control"
                                       value="<?= htmlspecialchars($_POST["phone"] ?? $employee["phone"]) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>

                                <select name="department_id" class="form-select">
                                    <option value="">Select Department</option>

                                    <?php while ($department = mysqli_fetch_assoc($departments)) { ?>
                                        <?php
                                        $selected_department =
                                            $_POST["department_id"]
                                            ?? $employee["department_id"];
                                        ?>

                                        <option value="<?= $department["id"] ?>"
                                            <?= ($selected_department == $department["id"]) ? "selected" : "" ?>>
                                            <?= htmlspecialchars($department["department_name"]) ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Gender</label>

                                <?php
                                $selected_gender =
                                    $_POST["gender"]
                                    ?? $employee["gender"];
                                ?>

                                <input type="radio" name="gender" value="Male"
                                    <?= $selected_gender == "Male" ? "checked" : "" ?>>
                                Male

                                <input type="radio" name="gender" value="Female" class="ms-3"
                                    <?= $selected_gender == "Female" ? "checked" : "" ?>>
                                Female

                                <input type="radio" name="gender" value="Other" class="ms-3"
                                    <?= $selected_gender == "Other" ? "checked" : "" ?>>
                                Other
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Salary</label>

                                <input type="number"
                                       step="0.01"
                                       min="0.01"
                                       name="salary"
                                       class="form-control"
                                       value="<?= htmlspecialchars($_POST["salary"] ?? $employee["salary"]) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Joining Date</label>

                                <input type="date"
                                       name="joining_date"
                                       class="form-control"
                                       value="<?= htmlspecialchars($_POST["joining_date"] ?? $employee["joining_date"]) ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Address</label>

                            <textarea name="address"
                                      class="form-control"
                                      rows="4"><?= htmlspecialchars($_POST["address"] ?? $employee["address"]) ?></textarea>
                        </div>

                        <button type="submit"
                                name="update_employee"
                                class="btn btn-warning">
                            Update Employee
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
<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

    if ($id <= 0) {
        header('Location: index.php');
        exit;
    }

    $departments = $pdo->query(
        'SELECT id, department_name FROM departments ORDER BY department_name'
    )->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT * FROM employees WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);
    $employee = $stmt->fetch();

    if (!$employee) {
        header('Location: index.php');
        exit;
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['employee_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $salary = trim($_POST['salary'] ?? '');
        $joiningDate = $_POST['joining_date'] ?? '';
        $departmentId = (int) ($_POST['department_id'] ?? 0);
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $email === '' || $salary === '' || $joiningDate === '' || $departmentId <= 0) {
            $error = 'Please complete all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid email address.';
        } elseif (!is_numeric($salary) || (float) $salary <= 0) {
            $error = 'Salary must be greater than zero.';
        } else {
            $emailCheck = $pdo->prepare(
                'SELECT id FROM employees
                WHERE email = :email AND id != :id'
            );
            $emailCheck->execute([
                'email' => $email,
                'id' => $id
            ]);

            if ($emailCheck->fetch()) {
                $error = 'This email already belongs to another employee.';
            } else {
                $update = $pdo->prepare(
                    'UPDATE employees SET
                        department_id = :department_id,
                        employee_name = :employee_name,
                        email = :email,
                        phone = :phone,
                        gender = :gender,
                        salary = :salary,
                        joining_date = :joining_date,
                        address = :address
                    WHERE id = :id'
                );

                $update->execute([
                    'department_id' => $departmentId,
                    'employee_name' => $name,
                    'email' => $email,
                    'phone' => $phone ?: null,
                    'gender' => $gender ?: null,
                    'salary' => $salary,
                    'joining_date' => $joiningDate,
                    'address' => $address ?: null,
                    'id' => $id
                ]);

                header('Location: index.php');
                exit;
            }
        }

        $employee = array_merge($employee, [
            "employee_name" => $name,
            "email" => $email,
            "phone" => $phone,
            "gender" => $gender,
            "salary" => $salary,
            "joining_date" => $joiningDate,
            "department_id" => $departmentId,
            "address" => $address
        ]);
    }

    $pageTitle = "Edit Employee";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 800px;">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center mb-4">
            <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; font-size:20px;">
                ✏️
            </span>
            <div>
                <h1 class="h3 fw-bold mb-0">Edit Employee</h1>
                <p class="text-muted mb-0">Update the details below</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <div>⚠️ <?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?= $employee['id'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" name="employee_name" id="employee_name" class="form-control"
                               placeholder="Employee Name" value="<?= htmlspecialchars($employee['employee_name']) ?>" required>
                        <label for="employee_name">Employee Name *</label>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="Email" value="<?= htmlspecialchars($employee['email']) ?>" required>
                        <label for="email">Email *</label>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" name="phone" id="phone" class="form-control"
                               placeholder="Phone" value="<?= htmlspecialchars($employee['phone'] ?? '') ?>">
                        <label for="phone">Phone</label>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <select name="gender" id="gender" class="form-select">
                            <option value="">Select gender</option>
                            <option value="Male" <?= $employee["gender"] === "Male" ? "selected" : "" ?>>Male</option>
                            <option value="Female" <?= $employee["gender"] === "Female" ? "selected" : "" ?>>Female</option>
                            <option value="Other" <?= $employee["gender"] === "Other" ? "selected" : "" ?>>Other</option>
                        </select>
                        <label for="gender">Gender</label>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="number" name="salary" id="salary" class="form-control"
                               placeholder="Salary" value="<?= htmlspecialchars($employee['salary']) ?>"
                               min="1" step="0.01" required>
                        <label for="salary">Salary (₹) *</label>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="date" name="joining_date" id="joining_date" class="form-control"
                               placeholder="Joining Date" value="<?= htmlspecialchars($employee['joining_date']) ?>" required>
                        <label for="joining_date">Joining Date *</label>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <div class="form-floating">
                        <select name="department_id" id="department_id" class="form-select" required>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['id'] ?>"
                                    <?= (int) $employee["department_id"] === (int) $department["id"] ? "selected" : "" ?>>
                                    <?= htmlspecialchars($department["department_name"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="department_id">Department *</label>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="form-floating">
                        <textarea name="address" id="address" class="form-control" placeholder="Address" style="height: 100px" rows="3"><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
                        <label for="address">Address</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-lg rounded-pill px-4">Update Employee</button>
                <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
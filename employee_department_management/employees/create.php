<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $departments = $pdo->query(
        'SELECT id, department_name FROM departments ORDER BY department_name'
    )->fetchAll();

    $name = '';
    $email = '';
    $phone = '';
    $gender = '';
    $salary = '';
    $joiningDate = '';
    $departmentId = '';
    $address = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['employee_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $salary = trim($_POST['salary'] ?? '');
        $joiningDate = $_POST['joining_date'] ?? '';
        $departmentId = $_POST['department_id'] ?? '';
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $email === '' || $salary === '' || $joiningDate === '' || $departmentId === '') {
            $error = 'Please complete all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid email address.';
        } elseif ($phone !== '' && !ctype_digit($phone)) {
            $error = 'Phone number must contain digits only.';
        } elseif (!is_numeric($salary) || (float) $salary <= 0) {
            $error = 'Salary must be greater than zero.';
        } elseif (!in_array($gender, ['', 'Male', 'Female', 'Other'], true)) {
            $error = 'Select a valid gender.';
        } else {
            $emailCheck = $pdo->prepare(
                'SELECT id FROM employees WHERE email = :email'
            );
            $emailCheck->execute(['email' => $email]);

            if ($emailCheck->fetch()) {
                $error = 'This employee email already exists.';
            } else {
                $departmentCheck = $pdo->prepare(
                    'SELECT id FROM departments WHERE id = :id'
                );
                $departmentCheck->execute(['id' => $departmentId]);

                if (!$departmentCheck->fetch()) {
                    $error = 'Select a valid department.';
                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO employees
                        (department_id, employee_name, email, phone, gender, salary, joining_date, address)
                        VALUES
                        (:department_id, :employee_name, :email, :phone, :gender, :salary, :joining_date, :address)'
                    );

                    $stmt->execute([
                        'department_id' => $departmentId,
                        'employee_name' => $name,
                        'email' => $email,
                        'phone' => $phone !== '' ? $phone : null,
                        'gender' => $gender !== '' ? $gender : null,
                        'salary' => $salary,
                        'joining_date' => $joiningDate,
                        'address' => $address !== '' ? $address : null
                    ]);

                    header('Location: index.php');
                    exit;
                }
            }
        }
    }
?>

<?php

    $pageTitle = "Create Employees";
    require_once __DIR__ . "/../includes/header.php";
?>

        <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 800px;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; font-size:20px;">
                        👤
                    </span>
                    <div>
                        <h1 class="h3 fw-bold mb-0">Add Employee</h1>
                        <p class="text-muted mb-0">Fill in the details to create a new record</p>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>⚠️ <?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="employee_name" id="employee_name" class="form-control"
                                       placeholder="Employee Name" value="<?= htmlspecialchars($name) ?>" required>
                                <label for="employee_name">Employee Name *</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="email" name="email" id="email" class="form-control"
                                       placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
                                <label for="email">Email *</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" name="phone" id="phone" class="form-control"
                                       placeholder="Phone" value="<?= htmlspecialchars($phone) ?>">
                                <label for="phone">Phone</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <select name="gender" id="gender" class="form-select">
                                    <option value="">Select gender</option>
                                    <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                                <label for="gender">Gender</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" name="salary" id="salary" class="form-control"
                                       placeholder="Salary" value="<?= htmlspecialchars($salary) ?>" min="1" step="0.01" required>
                                <label for="salary">Salary (₹) *</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="date" name="joining_date" id="joining_date" class="form-control"
                                       placeholder="Joining Date" value="<?= htmlspecialchars($joiningDate) ?>" required>
                                <label for="joining_date">Joining Date *</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <select name="department_id" id="department_id" class="form-select" required>
                                    <option value="">Select department</option>

                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department['id'] ?>"
                                            <?= (string) $departmentId === (string) $department['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($department['department_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="department_id">Department *</label>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="form-floating">
                                <textarea name="address" id="address" class="form-control" placeholder="Address" style="height: 100px" rows="3"><?= htmlspecialchars($address) ?></textarea>
                                <label for="address">Address</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill px-4">
                            Save Employee
                        </button>

                        <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $search = trim($_GET['search'] ?? '');
    $departmentId = (int) ($_GET['department_id'] ?? 0);
    $sort = $_GET['sort'] ?? 'latest';

    $sortOptions = [
        'latest' => 'employees.joining_date DESC, employees.id DESC',
        'oldest' => 'employees.joining_date ASC, employees.id ASC',
        'name_asc' => 'employees.employee_name ASC',
        'name_desc' => 'employees.employee_name DESC',
        'salary_high' => 'employees.salary DESC',
        'salary_low' => 'employees.salary ASC'
    ];

    if (!array_key_exists($sort, $sortOptions)) {
        $sort = 'latest';
    }

    $departments = $pdo->query(
        'SELECT id, department_name FROM departments ORDER BY department_name'
    )->fetchAll();

    $sql = 'SELECT
                employees.id,
                employees.employee_name,
                employees.email,
                employees.phone,
                employees.salary,
                employees.joining_date,
                departments.department_name
            FROM employees
            INNER JOIN departments
                ON employees.department_id = departments.id
            WHERE 1 = 1';

    $params = [];

    if ($search !== '') {
        $sql .= ' AND (
            employees.employee_name LIKE :name_search
            OR employees.email LIKE :email_search
            OR departments.department_name LIKE :department_search
        )';

        $params['name_search'] = '%' . $search . '%';
        $params['email_search'] = '%' . $search . '%';
        $params['department_search'] = '%' . $search . '%';
    }

    if ($departmentId > 0) {
        $sql .= ' AND employees.department_id = :department_id';
        $params['department_id'] = $departmentId;
    }

    $sql .= ' ORDER BY ' . $sortOptions[$sort];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $employees = $stmt->fetchAll();

    $pageTitle = 'Employees';
    require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-dark rounded-pill px-3 py-2 mb-2">👥 EMPLOYEES</span>
        <h1 class="h3 fw-bold mb-1">Employees</h1>
        <p class="text-muted mb-0">Manage all employee records.</p>
    </div>

    <a href="create.php" class="btn btn-dark rounded-pill px-4">
        + Add Employee
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Search Employee</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Name, email, or department"
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Department</label>
                <select name="department_id" class="form-select">
                    <option value="0">All Departments</option>

                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department['id'] ?>"
                            <?= $departmentId === (int) $department['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>
                        Latest Joining
                    </option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>
                        Oldest Joining
                    </option>
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>
                        Name A-Z
                    </option>
                    <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>
                        Name Z-A
                    </option>
                    <option value="salary_high" <?= $sort === 'salary_high' ? 'selected' : '' ?>>
                        Salary High-Low
                    </option>
                    <option value="salary_low" <?= $sort === 'salary_low' ? 'selected' : '' ?>>
                        Salary Low-High
                    </option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark rounded-pill flex-fill">
                    Search
                </button>

                <a href="index.php" class="btn btn-outline-dark rounded-pill">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
        <p class="text-muted">
            <span class="badge bg-light text-dark border rounded-pill px-3"><?= count($employees) ?> employee(s) found</span>
        </p>

        <?php if ($employees): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted">
                            <th class="border-0">ID</th>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Phone</th>
                            <th class="border-0">Department</th>
                            <th class="border-0">Salary</th>
                            <th class="border-0">Joining Date</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td class="text-muted">#<?= $employee['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="bg-dark bg-opacity-10 text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:32px; height:32px; font-size:14px;">
                                            <?= strtoupper(substr($employee['employee_name'], 0, 1)) ?>
                                        </span>
                                        <span class="fw-semibold"><?= htmlspecialchars($employee['employee_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($employee['email']) ?></td>
                                <td><?= htmlspecialchars($employee['phone'] ?? '') ?></td>
                                <td><span class="badge bg-light text-dark border rounded-pill px-3"><?= htmlspecialchars($employee['department_name']) ?></span></td>
                                <td class="fw-semibold">₹<?= number_format($employee['salary'], 2) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($employee['joining_date']) ?></td>

                                <td class="text-end">
                                    <a href="edit.php?id=<?= $employee['id'] ?>"
                                       class="btn btn-outline-warning btn-sm rounded-pill">
                                        Edit
                                    </a>

                                    <form action="delete.php" method="post" class="d-inline"
                                          onsubmit="return confirm('Delete this employee?');">
                                        <input type="hidden" name="id" value="<?= $employee['id'] ?>">

                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="display-4 mb-3">🔍</div>
                <p class="text-muted mb-3">No employees match your search.</p>
                <a href="index.php" class="btn btn-outline-dark rounded-pill px-4">Show All Employees</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
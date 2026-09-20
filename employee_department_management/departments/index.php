<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $departments = $pdo->query(
        'SELECT id, department_name, department_code, description
        FROM departments
        ORDER BY department_name ASC'
    )->fetchAll();
?>

<?php
    $pageTitle = "Departments";
    require_once __DIR__ . "/../includes/header.php";
?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-dark rounded-pill px-3 py-2 mb-2">🏬 DEPARTMENTS</span>
                <h1 class="h3 fw-bold mb-0">Departments</h1>
            </div>

            <a href="create.php" class="btn btn-dark rounded-pill px-4">
                + Add Department
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <?php if ($departments): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted">
                                    <th class="border-0">ID</th>
                                    <th class="border-0">Department Name</th>
                                    <th class="border-0">Code</th>
                                    <th class="border-0">Description</th>
                                    <th class="border-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departments as $department): ?>
                                    <tr>
                                        <td class="text-muted">#<?= $department['id'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="bg-dark bg-opacity-10 text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:32px; height:32px; font-size:14px;">
                                                    🏬
                                                </span>
                                                <span class="fw-semibold"><?= htmlspecialchars($department['department_name']) ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border rounded-pill px-3"><?= htmlspecialchars($department['department_code']) ?></span></td>
                                        <td class="text-muted"><?= htmlspecialchars($department['description'] ?? '') ?></td>

                                        <td class="text-end">
                                            <a href="edit.php?id=<?= $department['id'] ?>" class="btn btn-outline-warning btn-sm rounded-pill">
                                                Edit
                                            </a>

                                            <form action="delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete this department?');">
                                                <input type="hidden" name="id" value="<?= $department['id'] ?>">

                                                <button class="btn btn-outline-danger btn-sm rounded-pill" type="submit">
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
                        <div class="display-4 mb-3">🏬</div>
                        <p class="text-muted mb-0">
                            No departments found. Click "Add Department" to create one.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
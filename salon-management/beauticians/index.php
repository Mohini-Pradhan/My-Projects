<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    $errors = [];
    $beauticianName = '';
    $phone = '';
    $specialization = '';
    $experience = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $beauticianName = trim($_POST['beautician_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $experience = trim($_POST['experience'] ?? '');

        if ($beauticianName === '' || $phone === '') {
            $errors[] = 'Beautician name and phone number are required.';
        }

        if ($experience !== '' && !ctype_digit($experience)) {
            $errors[] = 'Experience must be a whole number of years.';
        }

        if (empty($errors)) {
            $addBeautician = $pdo->prepare(
                'INSERT INTO beauticians
                (beautician_name, phone, specialization, experience)
                VALUES
                (:beautician_name, :phone, :specialization, :experience)'
            );

            $addBeautician->execute([
                'beautician_name' => $beauticianName,
                'phone' => $phone,
                'specialization' => $specialization !== '' ? $specialization : null,
                'experience' => $experience !== '' ? (int) $experience : 0
            ]);

            $_SESSION['success_message'] = 'Beautician added successfully.';
            redirect('/beauticians/index.php');
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['error_message']);

    $search = trim($_GET['search'] ?? '');

    if ($search !== '') {
        $getBeauticians = $pdo->prepare(
            'SELECT *
            FROM beauticians
            WHERE beautician_name LIKE :name_search
                OR phone LIKE :phone_search
                OR specialization LIKE :specialization_search
            ORDER BY id DESC'
        );

        $getBeauticians->execute([
            'name_search' => '%' . $search . '%',
            'phone_search' => '%' . $search . '%',
            'specialization_search' => '%' . $search . '%'
        ]);

        $beauticians = $getBeauticians->fetchAll();
    } else {
        $getBeauticians = $pdo->query(
            'SELECT *
            FROM beauticians
            ORDER BY id DESC'
        );

        $beauticians = $getBeauticians->fetchAll();
    }
?>

<?php
    $pageTitle = "Beauticians";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h4 mb-3">Add Beautician</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="beautician_name" class="form-label">
                                Beautician Name *
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="beautician_name"
                                name="beautician_name"
                                value="<?= e($beauticianName) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input
                                type="text"
                                class="form-control"
                                id="phone"
                                name="phone"
                                value="<?= e($phone) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="specialization" class="form-label">
                                Specialization
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="specialization"
                                name="specialization"
                                placeholder="Example: Hair styling"
                                value="<?= e($specialization) ?>"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="experience" class="form-label">
                                Experience in Years
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                id="experience"
                                name="experience"
                                min="0"
                                step="1"
                                value="<?= e($experience) ?>"
                            >
                        </div>

                        <button type="submit" class="btn text-white" style="background-color: #8f1d4c; width: 100%;">
                            Add Beautician
                        </button>

                    </form>

                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 mb-0">Beautician List</h2>
                        <span class="badge text-white" style="background-color: #4a0f2a;">
                            <?= count($beauticians) ?> Found
                        </span>
                    </div>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <?= e($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger">
                            <?= e($errorMessage) ?>
                        </div>
                    <?php endif; ?>

                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input
                                type="search"
                                class="form-control"
                                name="search"
                                placeholder="Search name, phone, or specialization"
                                value="<?= e($search) ?>"
                            >

                            <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                                Search
                            </button>

                            <?php if ($search !== ''): ?>
                                <a
                                    href="<?= BASE_URL ?>/beauticians/index.php"
                                    class="btn btn-outline-secondary"
                                >
                                    Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Specialization</th>
                                    <th>Experience</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($beauticians)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No beauticians found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($beauticians as $beautician): ?>
                                        <tr>
                                            <td><?= (int) $beautician['id'] ?></td>
                                            <td><?= e($beautician['beautician_name']) ?></td>
                                            <td><?= e($beautician['phone']) ?></td>
                                            <td><?= e($beautician['specialization'] ?? '-') ?></td>
                                            <td>
                                                <?= (int) $beautician['experience'] ?> years
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/beauticians/edit.php?id=<?= (int) $beautician['id'] ?>" class="btn text-white" style="background-color: #8f1d4c;">
                                                    Edit
                                                </a>
                                                <form method="POST" action="<?= BASE_URL ?>/beauticians/delete.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this beautician?');">
                                                    <input type="hidden" name="id" value="<?= (int) $beautician['id'] ?>">

                                                    <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

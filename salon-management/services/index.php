<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    $errors = [];
    $serviceName = '';
    $price = '';
    $duration = '';
    $description = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $serviceName = trim($_POST['service_name'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($serviceName === '' || $price === '' || $duration === '') {
            $errors[] = 'Service name, price, and duration are required.';
        }

        if ($price !== '' && (!is_numeric($price) || (float) $price < 0)) {
            $errors[] = 'Price must be a valid positive number.';
        }

        if ($duration !== '' && (!ctype_digit($duration) || (int) $duration <= 0)) {
            $errors[] = 'Duration must be a whole number of minutes.';
        }

        if (empty($errors)) {
            $checkService = $pdo->prepare(
                'SELECT id
                FROM services
                WHERE service_name = :service_name'
            );

            $checkService->execute([
                'service_name' => $serviceName
            ]);

            if ($checkService->fetch()) {
                $errors[] = 'A service with this name already exists.';
            } else {
                $addService = $pdo->prepare(
                    'INSERT INTO services
                    (service_name, price, duration, description)
                    VALUES
                    (:service_name, :price, :duration, :description)'
                );

                $addService->execute([
                    'service_name' => $serviceName,
                    'price' => (float) $price,
                    'duration' => (int) $duration,
                    'description' => $description !== '' ? $description : null
                ]);

                $_SESSION['success_message'] = 'Service added successfully.';
                redirect('/services/index.php');
            }
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['error_message']);

    $search = trim($_GET['search'] ?? '');

    if ($search !== '') {
        $getServices = $pdo->prepare(
            'SELECT *
            FROM services
            WHERE service_name LIKE :service_search
            ORDER BY id DESC'
        );

        $getServices->execute([
            'service_search' => '%' . $search . '%'
        ]);

        $services = $getServices->fetchAll();
    } else {
        $getServices = $pdo->query(
            'SELECT *
            FROM services
            ORDER BY id DESC'
        );

        $services = $getServices->fetchAll();
    }
?>

<?php
    $pageTitle = "Services";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h4 mb-3">Add Service</h1>

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
                            <label for="service_name" class="form-label">
                                Service Name *
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="service_name"
                                name="service_name"
                                value="<?= e($serviceName) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">
                                Price *
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                id="price"
                                name="price"
                                value="<?= e($price) ?>"
                                min="0"
                                step="0.01"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">
                                Duration in Minutes *
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                id="duration"
                                name="duration"
                                value="<?= e($duration) ?>"
                                min="1"
                                step="1"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="4"
                            ><?= e($description) ?></textarea>
                        </div>

                        <button type="submit" class="btn text-white" style="background-color: #8f1d4c; width: 100%;">
                            Add Service
                        </button>

                    </form>

                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 mb-0">Service List</h2>
                        <span class="badge text-white" style="background-color: #4a0f2a;">
                            <?= count($services) ?> Found
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
                                placeholder="Search services"
                                value="<?= e($search) ?>"
                            >

                            <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                                Search
                            </button>

                            <?php if ($search !== ''): ?>
                                <a
                                    href="<?= BASE_URL ?>/services/index.php"
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
                                    <th>Sl.No</th>
                                    <th>Service</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($services)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No services found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($services as $service): ?>
                                        <tr>
                                            <td><?= (int) $service['id'] ?></td>
                                            <td><?= e($service['service_name']) ?></td>
                                            <td>
                                                ₹<?= e(number_format(
                                                    (float) $service['price'],
                                                    2
                                                )) ?>
                                            </td>
                                            <td>
                                                <?= (int) $service['duration'] ?> min
                                            </td>
                                            <td><?= e($service['description'] ?? '-') ?></td>
                                            <td class="text-nowrap">
                                                <a href="<?= BASE_URL ?>/services/edit.php?id=<?= (int) $service['id'] ?>" class="btn text-white" style="background-color: #8f1d4c;">
                                                    Edit
                                                </a>
                                                 <form method="POST" action="<?= BASE_URL ?>/services/delete.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                    <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">

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

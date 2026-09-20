<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        $_SESSION['error_message'] = 'Invalid service selected.';
        redirect('/services/index.php');
    }

    $serviceId = (int) $_GET['id'];

    $getService = $pdo->prepare(
        'SELECT *
        FROM services
        WHERE id = :id'
    );

    $getService->execute(['id' => $serviceId]);
    $service = $getService->fetch();

    if (!$service) {
        $_SESSION['error_message'] = 'Service not found.';
        redirect('/services/index.php');
    }

    $errors = [];

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
            $checkName = $pdo->prepare(
                'SELECT id
                FROM services
                WHERE service_name = :service_name
                AND id != :id'
            );

            $checkName->execute([
                'service_name' => $serviceName,
                'id' => $serviceId
            ]);

            if ($checkName->fetch()) {
                $errors[] = 'Another service already uses this name.';
            } else {
                $updateService = $pdo->prepare(
                    'UPDATE services
                    SET service_name = :service_name,
                        price = :price,
                        duration = :duration,
                        description = :description
                    WHERE id = :id'
                );

                $updateService->execute([
                    'service_name' => $serviceName,
                    'price' => (float) $price,
                    'duration' => (int) $duration,
                    'description' => $description !== '' ? $description : null,
                    'id' => $serviceId
                ]);

                $_SESSION['success_message'] = 'Service updated successfully.';
                redirect('/services/index.php');
            }
        }

        $service['service_name'] = $serviceName;
        $service['price'] = $price;
        $service['duration'] = $duration;
        $service['description'] = $description;
    }
?>

<?php
    $pageTitle = "Edit Service";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h3 mb-4">Edit Service</h1>

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
                                value="<?= e($service['service_name']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price *</label>
                            <input
                                type="number"
                                class="form-control"
                                id="price"
                                name="price"
                                value="<?= e((string) $service['price']) ?>"
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
                                value="<?= (int) $service['duration'] ?>"
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
                            ><?= e($service['description']) ?></textarea>
                        </div>

                        <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                            Update Service
                        </button>

                        <a href="<?= BASE_URL ?>/services/index.php" class="btn text-white" style="background-color: #c12f68;">
                            Cancel
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

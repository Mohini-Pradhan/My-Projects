<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        $_SESSION['error_message'] = 'Invalid beautician selected.';
        redirect('/beauticians/index.php');
    }

    $beauticianId = (int) $_GET['id'];

    $getBeautician = $pdo->prepare(
        'SELECT *
        FROM beauticians
        WHERE id = :id'
    );

    $getBeautician->execute(['id' => $beauticianId]);
    $beautician = $getBeautician->fetch();

    if (!$beautician) {
        $_SESSION['error_message'] = 'Beautician not found.';
        redirect('/beauticians/index.php');
    }

    $errors = [];

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
            $updateBeautician = $pdo->prepare(
                'UPDATE beauticians
                SET beautician_name = :beautician_name,
                    phone = :phone,
                    specialization = :specialization,
                    experience = :experience
                WHERE id = :id'
            );

            $updateBeautician->execute([
                'beautician_name' => $beauticianName,
                'phone' => $phone,
                'specialization' => $specialization !== '' ? $specialization : null,
                'experience' => $experience !== '' ? (int) $experience : 0,
                'id' => $beauticianId
            ]);

            $_SESSION['success_message'] = 'Beautician updated successfully.';
            redirect('/beauticians/index.php');
        }

        $beautician['beautician_name'] = $beauticianName;
        $beautician['phone'] = $phone;
        $beautician['specialization'] = $specialization;
        $beautician['experience'] = $experience;
    }
?>

<?php
    $pageTitle = "Edit Beautician";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h3 mb-4">Edit Beautician</h1>

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
                                value="<?= e($beautician['beautician_name']) ?>"
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
                                value="<?= e($beautician['phone']) ?>"
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
                                value="<?= e($beautician['specialization']) ?>"
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
                                value="<?= (int) $beautician['experience'] ?>"
                            >
                        </div>

                        <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                            Update Beautician
                        </button>

                        <a
                            href="<?= BASE_URL ?>/beauticians/index.php"
                            class="btn text-white" style="background-color: #c12f68;"
                        >
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

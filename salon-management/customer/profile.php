<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireCustomer();

$getCustomer = $pdo->prepare(
    'SELECT
        users.name,
        users.email,
        users.phone,
        customers.address,
        customers.gender
     FROM users
     INNER JOIN customers
        ON customers.user_id = users.id
     WHERE users.id = :user_id
     LIMIT 1'
);

$getCustomer->execute([
    'user_id' => (int) $_SESSION['user_id']
]);

$customer = $getCustomer->fetch();

if (!$customer) {
    $_SESSION['error_message'] = 'Customer profile not found.';
    redirect('/customer/index.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = $_POST['gender'] ?? '';

    $allowedGenders = [
        'Female',
        'Male',
        'Other',
        'Prefer not to say'
    ];

    if ($name === '' || $phone === '') {
        $errors[] = 'Name and phone number are required.';
    }

    if ($gender !== '' && !in_array($gender, $allowedGenders, true)) {
        $errors[] = 'Please choose a valid gender.';
    }

    if (!$errors) {
        $pdo->beginTransaction();

        try {
            $updateUser = $pdo->prepare(
                'UPDATE users
                 SET name = :name,
                     phone = :phone
                 WHERE id = :user_id'
            );

            $updateUser->execute([
                'name' => $name,
                'phone' => $phone,
                'user_id' => (int) $_SESSION['user_id']
            ]);

            $updateCustomer = $pdo->prepare(
                'UPDATE customers
                 SET customer_name = :name,
                     phone = :phone,
                     address = :address,
                     gender = :gender
                 WHERE user_id = :user_id'
            );

            $updateCustomer->execute([
                'name' => $name,
                'phone' => $phone,
                'address' => $address !== '' ? $address : null,
                'gender' => $gender !== '' ? $gender : null,
                'user_id' => (int) $_SESSION['user_id']
            ]);

            $pdo->commit();

            $_SESSION['user_name'] = $name;
            $success = 'Profile updated successfully.';

            $getCustomer->execute([
                'user_id' => (int) $_SESSION['user_id']
            ]);

            $customer = $getCustomer->fetch();
        } catch (Throwable $error) {
            $pdo->rollBack();
            $errors[] = 'Could not update profile. Please try again.';
        }
    }
}

$pageTitle = 'My Profile';
require_once __DIR__ . '/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <h1 class="h3 mb-2">My Profile</h1>

                    <p class="text-muted mb-4">
                        View and update your customer details.
                    </p>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?= e($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= e($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label" for="name">
                                Full Name
                            </label>

                            <input
                                class="form-control"
                                type="text"
                                id="name"
                                name="name"
                                value="<?= e($customer['name']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">
                                Email Address
                            </label>

                            <input
                                class="form-control"
                                type="email"
                                id="email"
                                value="<?= e($customer['email']) ?>"
                                disabled
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="phone">
                                Phone Number
                            </label>

                            <input
                                class="form-control"
                                type="text"
                                id="phone"
                                name="phone"
                                value="<?= e($customer['phone']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="gender">
                                Gender
                            </label>

                            <select class="form-select" id="gender" name="gender">

                                <option value="" <?= empty($customer['gender']) ? 'selected' : '' ?>>
                                    Select gender
                                </option>

                                <?php foreach ($allowedGenders as $gender): ?>
                                    <option
                                        value="<?= e($gender) ?>"
                                        <?= $customer['gender'] === $gender ? 'selected' : '' ?>
                                    >
                                        <?= e($gender) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="address">
                                Address
                            </label>

                            <textarea
                                class="form-control"
                                id="address"
                                name="address"
                                rows="3"
                            ><?= e($customer['address']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>

                        <a
                            href="<?= BASE_URL ?>/customer/appointments.php"
                            class="btn btn-outline-secondary"
                        >
                            Back
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
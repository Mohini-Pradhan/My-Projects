<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireCustomer();

$stmt = $pdo->prepare(
    'SELECT
        users.name,
        users.email,
        users.phone,
        customers.address,
        customers.gender,
        customers.created_at
    FROM users
    INNER JOIN customers ON customers.user_id = users.id
    WHERE users.id = :user_id
    LIMIT 1'
);

$stmt->execute([
    'user_id' => (int) $_SESSION['user_id']
]);

$customer = $stmt->fetch();

if (!$customer) {
    $_SESSION['error_message'] = 'Customer profile not found.';
    redirect('/customer/index.php');
}

$pageTitle = 'My Details';
require_once __DIR__ . '/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-1">My Details</h1>
                            <p class="text-muted mb-0">
                                Your customer profile information.
                            </p>
                        </div>

                        <a href="<?= BASE_URL ?>/customer/profile.php"
                           class="btn text-white"
                           style="background-color: #8f1d4c;">
                            Update Profile
                        </a>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-muted">Full Name</small>
                            <p class="fw-bold mb-0"><?= e($customer['name']) ?></p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Email Address</small>
                            <p class="fw-bold mb-0"><?= e($customer['email']) ?></p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Phone Number</small>
                            <p class="fw-bold mb-0"><?= e($customer['phone']) ?></p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Gender</small>
                            <p class="fw-bold mb-0">
                                <?= e($customer['gender'] ?: '-') ?>
                            </p>
                        </div>

                        <div class="col-12">
                            <small class="text-muted">Address</small>
                            <p class="fw-bold mb-0">
                                <?= e($customer['address'] ?: '-') ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Account Created</small>
                            <p class="fw-bold mb-0">
                                <?= e(date('d M Y', strtotime($customer['created_at']))) ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>
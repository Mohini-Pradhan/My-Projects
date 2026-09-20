<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    if (isCustomer()) {
        redirect('/customer/appointments.php');
    }

    if (isAdmin()) {
        redirect('/index.php');
    }

    $email = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = "Please enter your email and password.";
        } else {
            $getCustomer = $pdo->prepare(
                'SELECT id, name, email, password, role
                FROM users
                WHERE email = :email
                AND role = "customer"
                LIMIT 1'
            );

            $getCustomer->execute(['email' => $email]);
            $customer = $getCustomer->fetch();

            if (!$customer || !password_verify($password, $customer['password'])) {
                $error = 'Customer email or password is incorrect.';
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $customer['id'];
                $_SESSION['user_name'] = $customer['name'];
                $_SESSION['user_email'] = $customer['email'];
                $_SESSION['user_role'] = 'customer';

                redirect('/customer/appointments.php');
            }
        }
    }

    $pageTitle = 'Customer Login';
    require_once __DIR__ . '/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">

                    <div class="text-center mb-4">
                        <div class="brand-icon mx-auto mb-3">✦</div>

                        <h1 class="h3 mb-2">Customer Login</h1>

                        <p class="text-muted mb-0">
                            View your bookings, payments, and salon details.
                        </p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address
                            </label>

                            <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Login to My Account
                        </button>
                    </form>

                    <p class="text-center small mt-4 mb-0">
                        New customer?
                        <a href="<?= BASE_URL ?>/customer/register.php">
                            Create an account
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
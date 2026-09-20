<?php
    /*declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    if (isLoggedIn()) {
        redirect('/index.php');
    }

    $errors = [];
    $name = '';
    $email = '';
    $phone = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $errors[] = 'Please fill in all required fields.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            $checkUser = $pdo->prepare(
                'SELECT id
                FROM users
                WHERE email = :email'
            );

            $checkUser->execute([
                'email' => $email
            ]);

            if ($checkUser->fetch()) {
                $errors[] = 'This email is already registered.';
            } else {
                $addUser = $pdo->prepare(
                    'INSERT INTO users (name, email, phone, password, role)
                    VALUES (:name, :email, :phone, :password, :role)'
                );

                $addUser->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone !== '' ? $phone : null,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'admin'
                ]);

                $_SESSION['success_message'] =
                    'Registration successful. Please login.';

                redirect('/auth/login.php');
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Create Account | Salon Management</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            :root {
                --salon-dark: #4a0f2a;
                --salon-primary: #8f1d4c;
                --salon-pink: #c12f68;
            }

            body {
                background: linear-gradient(
                    135deg,
                    var(--salon-dark),
                    var(--salon-primary),
                    var(--salon-pink)
                );
            }

            .register-section {
                min-height: 100vh;
            }

            .register-card {
                overflow: hidden;
                border: 0;
                border-radius: 1.25rem;
            }

            .register-image {
                width: 100%;
                height: 100%;
                min-height: 650px;
                object-fit: cover;
            }

            .brand-icon {
                width: 48px;
                height: 48px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #ffffff;
                background: var(--salon-primary);
                border-radius: 50%;
                font-size: 1.35rem;
            }

            .btn-salon {
                color: #ffffff;
                background: var(--salon-primary);
                border-color: var(--salon-primary);
            }

            .btn-salon:hover {
                color: #ffffff;
                background: var(--salon-dark);
                border-color: var(--salon-dark);
            }

            .form-control:focus {
                border-color: var(--salon-pink);
                box-shadow: 0 0 0 0.25rem rgba(193, 47, 104, 0.15);
            }

            .register-heading {
                color: var(--salon-dark);
            }
        </style>
    </head>

    <body>

        <section class="register-section d-flex align-items-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-11">

                        <div class="card register-card shadow-lg">
                            <div class="row g-0">

                                <div class="col-md-5 d-none d-md-block">
                                    <img
                                        src="<?= BASE_URL ?>/assets/images/salon-login.jpg"
                                        alt="Salon beauty services"
                                        class="register-image"
                                    >
                                </div>

                                <div class="col-md-7 d-flex align-items-center">
                                    <div class="card-body p-4 p-lg-5">

                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <div class="brand-icon">✦</div>

                                            <div>
                                                <h1 class="h3 fw-bold mb-0 register-heading">
                                                    Create Admin Account
                                                </h1>

                                                <p class="text-muted mb-0">
                                                    Salon & Beauty Studio Management
                                                </p>
                                            </div>
                                        </div>

                                        <p class="text-muted mb-4">
                                            Create an account to access your salon dashboard.
                                        </p>

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
                                                <label for="name" class="form-label">
                                                    Full Name *
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control form-control-lg"
                                                    id="name"
                                                    name="name"
                                                    value="<?= e($name) ?>"
                                                    required
                                                >
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label">
                                                    Email Address *
                                                </label>

                                                <input
                                                    type="email"
                                                    class="form-control form-control-lg"
                                                    id="email"
                                                    name="email"
                                                    value="<?= e($email) ?>"
                                                    required
                                                >
                                            </div>

                                            <div class="mb-3">
                                                <label for="phone" class="form-label">
                                                    Phone Number
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control form-control-lg"
                                                    id="phone"
                                                    name="phone"
                                                    value="<?= e($phone) ?>"
                                                >
                                            </div>

                                            <div class="mb-3">
                                                <label for="password" class="form-label">
                                                    Password *
                                                </label>

                                                <input
                                                    type="password"
                                                    class="form-control form-control-lg"
                                                    id="password"
                                                    name="password"
                                                    minlength="6"
                                                    required
                                                >
                                            </div>

                                            <div class="mb-4">
                                                <label
                                                    for="confirm_password"
                                                    class="form-label"
                                                >
                                                    Confirm Password *
                                                </label>

                                                <input
                                                    type="password"
                                                    class="form-control form-control-lg"
                                                    id="confirm_password"
                                                    name="confirm_password"
                                                    minlength="6"
                                                    required
                                                >
                                            </div>

                                            <button
                                                type="submit"
                                                class="btn btn-salon btn-lg w-100"
                                            >
                                                Create Account
                                            </button>

                                        </form>

                                        <p class="text-center text-muted mt-4 mb-0">
                                            Already have an account?
                                            <a
                                                href="<?= BASE_URL ?>/auth/login.php"
                                                class="text-decoration-none fw-semibold"
                                                style="color: #8f1d4c;"
                                            >
                                                Login here
                                            </a>
                                        </p>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </body>
</html>

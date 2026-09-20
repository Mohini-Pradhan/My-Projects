<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirect(isCustomer() ? '/customer/index.php' : '/index.php');
}

$email = '';
$error = '';

$successMessage = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        $getUser = $pdo->prepare(
            'SELECT id, name, email, password, role
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $getUser->execute(['email' => $email]);
        $user = $getUser->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            redirect($user['role'] === 'customer' ? '/customer/index.php' : '/index.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Salon Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        :root {
            --wine: #5a1131;
            --pink: #a40c4f;
            --soft-pink: #f9e8ef;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 85% 25%, #b91561 0%, transparent 25%),
                radial-gradient(circle at 15% 90%, #52152d 0%, transparent 35%),
                linear-gradient(135deg, #201a1c, #46162d, #8e0e45);
        }

        .login-page {
            min-height: 100vh;
            padding: 45px 15px;
        }

        .login-card {
            overflow: hidden;
            background: #ffffff;
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
        }

        .salon-image {
            width: 100%;
            height: 100%;
            min-height: 680px;
            object-fit: cover;
        }

        .form-panel {
            min-height: 680px;
            padding: 70px 70px 40px;
        }

        .logo-circle {
            width: 112px;
            height: 112px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #ffffff;
            background: linear-gradient(135deg, var(--wine), var(--pink));
            border-radius: 50%;
            font-size: 3rem;
            box-shadow: 0 10px 25px rgba(130, 10, 65, 0.25);
        }

        .brand-title {
            color: var(--wine);
            font-size: 2rem;
            font-weight: 700;
        }

        .brand-subtitle {
            color: #737373;
            font-size: 1.1rem;
        }

        .input-group-text {
            color: #8a8a8a;
            background: #ffffff;
            border-right: 0;
        }

        .form-control {
            border-left: 0;
        }

        .input-group:focus-within {
            border-radius: 0.65rem;
            box-shadow: 0 0 0 0.25rem rgba(164, 12, 79, 0.14);
        }

        .input-group:focus-within .form-control,
        .input-group:focus-within .input-group-text {
            border-color: var(--pink);
            box-shadow: none;
        }

        .form-control,
        .input-group-text {
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .btn-login {
            padding: 0.9rem;
            color: #ffffff;
            background: linear-gradient(90deg, #990a48, #b81259);
            border: 0;
            border-radius: 0.65rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .btn-login:hover {
            color: #ffffff;
            background: linear-gradient(90deg, #710631, #980842);
        }

        .link-pink {
            color: var(--pink);
            font-weight: 600;
        }

        .link-pink:hover {
            color: var(--wine);
        }

        @media (max-width: 767px) {
            .form-panel {
                min-height: auto;
                padding: 45px 28px 35px;
            }

            .brand-title {
                font-size: 1.55rem;
            }
        }
    </style>
</head>

<body>

<section class="login-page d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-11 col-xxl-10">

                <div class="card login-card">
                    <div class="row g-0">

                        <div class="col-md-6 d-none d-md-block">
                            <img
                                src="<?= BASE_URL ?>/assets/images/salon-login.jpg"
                                alt="Beauty salon interior"
                                class="salon-image"
                            >
                        </div>

                        <div class="col-md-6">
                            <div class="form-panel d-flex flex-column justify-content-center">

                                <div class="text-center mb-4">
                                    <div class="logo-circle">
                                        <i class="bi bi-stars"></i>
                                    </div>

                                    <h1 class="brand-title mb-2">
                                        Salon Management System
                                    </h1>

                                    <p class="brand-subtitle mb-0">
                                        Manage your salon easily
                                    </p>
                                </div>

                                <?php if ($successMessage): ?>
                                    <div class="alert alert-success">
                                        <?= e($successMessage) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($error): ?>
                                    <div class="alert alert-danger">
                                        <?= e($error) ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST">

                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope fs-5"></i>
                                        </span>

                                        <input
                                            type="email"
                                            class="form-control form-control-lg"
                                            name="email"
                                            placeholder="Email address"
                                            value="<?= e($email) ?>"
                                            required
                                            autofocus
                                        >
                                    </div>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <i class="bi bi-lock fs-5"></i>
                                        </span>

                                        <input
                                            type="password"
                                            class="form-control form-control-lg"
                                            id="password"
                                            name="password"
                                            placeholder="Password"
                                            required
                                        >

                                        <button
                                            type="button"
                                            class="input-group-text border-start-0"
                                            id="togglePassword"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="small text-muted">
                                            Secure salon access
                                        </span>

                                        <a href="<?= BASE_URL ?>/auth/register.php" class="small text-decoration-none link-pink">
                                            Create account
                                        </a>

                                        <a href="<?= BASE_URL ?>/index.php" class="small text-decoration-none link-pink">
                                            Continue as Customer
                                        </a>

                                    </div>

                                    <button type="submit" class="btn btn-login w-100">
                                        Login
                                    </button>

                                </form>

                                <p class="text-center text-muted small mt-5 mb-0">
                                    &copy; <?= date('Y') ?> Salon Management System.
                                    All rights reserved.
                                </p>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const isPassword = password.type === 'password';

        password.type = isPassword ? 'text' : 'password';

        this.innerHTML = isPassword
            ? '<i class="bi bi-eye-slash"></i>'
            : '<i class="bi bi-eye"></i>';
    });
</script>

</body>
</html>

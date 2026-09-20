<?php
    session_start();

    require_once __DIR__ . '/../config/db.php';

    $email = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Email and password are required.';
        } else {
            $stmt = $pdo->prepare(
                'SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1'
            );

            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header('Location: ../index.php');
                exit;
            }

            $error = 'Invalid email or password.';
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid vh-100">
        <div class="row vh-100 g-0">

            <!-- Left branding panel -->
            <div class="col-lg-6 d-none d-lg-flex bg-primary bg-gradient text-white flex-column justify-content-center align-items-center p-5">
                <div class="text-center" style="max-width: 420px;">
                    <div class="display-1 mb-4">🎓</div>
                    <h1 class="display-5 fw-bold mb-3">SCMS Institute</h1>
                    <p class="fs-5 opacity-75 mb-4">
                        Employee Department Management System
                    </p>
                    <hr class="border-light opacity-50 w-25 mx-auto mb-4">
                    <p class="opacity-75">
                        Manage students, courses, and enrollments all in one place — fast, simple, and secure.
                    </p>
                </div>
            </div>

            <!-- Right login form panel -->
            <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center bg-light p-4">
                <div class="w-100" style="max-width: 420px;">

                    <div class="text-center mb-4 d-lg-none">
                        <div class="display-3">🎓</div>
                        <h4 class="fw-bold">SCMS Institute</h4>
                    </div>

                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-5">
                            <h1 class="h3 fw-bold text-center mb-1">Welcome Back</h1>
                            <p class="text-muted text-center mb-4">
                                Sign in to continue to your dashboard
                            </p>

                            <?php if ($error): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <div>⚠️ <?= htmlspecialchars($error) ?></div>
                                </div>
                            <?php endif; ?>

                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white">✉️</span>
                                        <input type="email" name="email" class="form-control" placeholder="you@example.com" value="<?= htmlspecialchars($email) ?>" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Password</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white">🔒</span>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm">
                                    Login
                                </button>
                            </form>

                            <p class="text-center text-muted mt-4 mb-0">
                                Don't have an account? <a href="register.php" class="text-decoration-none fw-semibold">Register here</a>
                            </p>
                        </div>
                    </div>

                    <p class="text-center text-muted mt-4 small mb-0">
                        © <?= date('Y') ?> SCMS Institute. All rights reserved.
                    </p>

                </div>
            </div>

        </div>
    </div>
</body>
</html>
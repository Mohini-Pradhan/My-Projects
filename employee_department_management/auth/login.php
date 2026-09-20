<?php
    session_start();

    require_once __DIR__ . '/../config/database.php';

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

<body class="bg-dark bg-gradient">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">

            <div class="card border-0 shadow-lg rounded-5 position-relative">

                <div class="position-absolute top-0 start-50 translate-middle">
                    <div class="bg-white rounded-circle shadow d-inline-flex align-items-center justify-content-center p-4">
                        <span class="fs-1">🏢</span>
                    </div>
                </div>

                <div class="card-body p-5 pt-5 mt-3">

                    <h1 class="h3 fw-bold text-center mb-1 mt-3">Welcome Back</h1>
                    <p class="text-muted text-center mb-4">
                        Employee Department Management System
                    </p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>⚠️ <?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="form-floating mb-3">
                            <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($email) ?>" required>
                            <label for="email">Email Address</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>

                        <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm">
                            Sign In
                        </button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        Don't have an account? <a href="register.php" class="text-decoration-none fw-semibold text-dark">Register here</a>
                    </p>

                </div>
            </div>

            <p class="text-center text-white-50 mt-4 small mb-0">
                © <?= date('Y') ?> Employee Department Management System. All rights reserved.
            </p>

        </div>
    </div>
</body>
</html>
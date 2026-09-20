<?php
    session_start();

    require_once __DIR__ . '/../config/database.php';

    $name = '';
    $email = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must contain at least 6 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            $checkUser = $pdo->prepare(
                'SELECT id FROM users WHERE email = :email'
            );
            $checkUser->execute(['email' => $email]);

            if ($checkUser->fetch()) {
                $error = 'This email is already registered.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, password)
                    VALUES (:name, :email, :password)'
                );

                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $passwordHash
                ]);

                header('Location: login.php?registered=1');
                exit;
            }
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark bg-gradient">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">

            <div class="card border-0 shadow-lg rounded-5 position-relative">

                <div class="position-absolute top-0 start-50 translate-middle">
                    <div class="bg-white rounded-circle shadow d-inline-flex align-items-center justify-content-center p-4">
                        <span class="fs-1">📝</span>
                    </div>
                </div>

                <div class="card-body p-5 pt-5 mt-3">

                    <h1 class="h3 fw-bold text-center mb-1 mt-3">Create Account</h1>
                    <p class="text-muted text-center mb-4">
                        Register a new admin for the system
                    </p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <div>⚠️ <?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="form-floating mb-3">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" value="<?= htmlspecialchars($name) ?>" required>
                            <label for="name">Full Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($email) ?>" required>
                            <label for="email">Email Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                            <label for="confirm_password">Confirm Password</label>
                        </div>

                        <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm">
                            Register
                        </button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        Already registered? <a href="login.php" class="text-decoration-none fw-semibold text-dark">Login here</a>
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
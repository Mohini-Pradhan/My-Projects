```php
<?php
    session_start();

    require_once __DIR__ . '/../config/db.php';

    $errors = [];

    $name = '';
    $email = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if ($name === '') {
            $errors[] = "Name is required.";
        }

        if ($email === '') {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        if ($password === '') {
            $errors[] = "Password is required.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }

        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // Check email already exists
        if (empty($errors)) {

            $stmt = $pdo->prepare(
                'SELECT id FROM users WHERE email = :email LIMIT 1'
            );

            $stmt->execute([
                'email' => $email
            ]);

            if ($stmt->fetch()) {
                $errors[] = "Email is already registered.";
            }
        }

        // Insert user
        if (empty($errors)) {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password)
                 VALUES (:name, :email, :password)'
            );

            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            $_SESSION['success'] = "Registration successful! Please login.";

            header('Location: login.php');
            exit;
        }
    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Register</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

    <div class="container-fluid vh-100">

        <div class="row vh-100 g-0">

            <!-- Left Branding Panel -->
            <div
                class="col-lg-6 d-none d-lg-flex bg-primary bg-gradient text-white flex-column justify-content-center align-items-center p-5"
            >

                <div class="text-center" style="max-width: 420px;">

                    <div class="display-1 mb-4">
                        🎓
                    </div>

                    <h1 class="display-5 fw-bold mb-3">
                        SCMS Institute
                    </h1>

                    <p class="fs-5 opacity-75 mb-4">
                        Employee Department Management System
                    </p>

                    <hr class="border-light opacity-50 w-25 mx-auto mb-4">

                    <p class="opacity-75">
                        Manage students, courses, and enrollments
                        all in one place — fast, simple, and secure.
                    </p>

                </div>

            </div>


            <!-- Right Register Panel -->
            <div
                class="col-lg-6 d-flex flex-column justify-content-center align-items-center bg-light p-4"
            >

                <div class="w-100" style="max-width: 420px;">

                    <!-- Mobile Branding -->
                    <div class="text-center mb-4 d-lg-none">

                        <div class="display-3">
                            🎓
                        </div>

                        <h4 class="fw-bold">
                            SCMS Institute
                        </h4>

                    </div>


                    <!-- Register Card -->
                    <div class="card border-0 shadow-lg rounded-4">

                        <div class="card-body p-5">

                            <h1 class="h3 fw-bold text-center mb-1">
                                Create Account
                            </h1>

                            <p class="text-muted text-center mb-4">
                                Register to access your dashboard
                            </p>


                            <!-- Success Message -->
                            <?php if (!empty($_SESSION['success'])): ?>

                                <div
                                    class="alert alert-success d-flex align-items-center"
                                    role="alert"
                                >
                                    <div>
                                        ✅ <?= htmlspecialchars($_SESSION['success']) ?>
                                    </div>
                                </div>

                                <?php unset($_SESSION['success']); ?>

                            <?php endif; ?>


                            <!-- Error Messages -->
                            <?php if (!empty($errors)): ?>

                                <div
                                    class="alert alert-danger"
                                    role="alert"
                                >

                                    <ul class="mb-0">

                                        <?php foreach ($errors as $error): ?>

                                            <li>
                                                <?= htmlspecialchars($error) ?>
                                            </li>

                                        <?php endforeach; ?>

                                    </ul>

                                </div>

                            <?php endif; ?>


                            <!-- Register Form -->
                            <form method="post" action="">

                                <!-- Full Name -->
                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Full Name
                                    </label>

                                    <div class="input-group input-group-lg">

                                        <span class="input-group-text bg-white">
                                            👤
                                        </span>

                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control"
                                            placeholder="Enter your full name"
                                            value="<?= htmlspecialchars($name) ?>"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Email -->
                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Email Address
                                    </label>

                                    <div class="input-group input-group-lg">

                                        <span class="input-group-text bg-white">
                                            ✉️
                                        </span>

                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            placeholder="you@example.com"
                                            value="<?= htmlspecialchars($email) ?>"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Password -->
                                <div class="mb-3">

                                    <label class="form-label fw-semibold">
                                        Password
                                    </label>

                                    <div class="input-group input-group-lg">

                                        <span class="input-group-text bg-white">
                                            🔒
                                        </span>

                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control"
                                            placeholder="••••••••"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Confirm Password -->
                                <div class="mb-4">

                                    <label class="form-label fw-semibold">
                                        Confirm Password
                                    </label>

                                    <div class="input-group input-group-lg">

                                        <span class="input-group-text bg-white">
                                            🔐
                                        </span>

                                        <input
                                            type="password"
                                            name="confirm_password"
                                            class="form-control"
                                            placeholder="••••••••"
                                            required
                                        >

                                    </div>

                                </div>


                                <!-- Register Button -->
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm"
                                >
                                    Register
                                </button>

                            </form>


                            <!-- Login Link -->
                            <p class="text-center text-muted mt-4 mb-0">

                                Already have an account?

                                <a
                                    href="login.php"
                                    class="text-decoration-none fw-semibold"
                                >
                                    Login here
                                </a>

                            </p>

                        </div>

                    </div>


                    <!-- Footer -->
                    <p class="text-center text-muted mt-4 small mb-0">

                        © <?= date('Y') ?> SCMS Institute.
                        All rights reserved.

                    </p>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
```

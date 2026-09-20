<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: auth/login.php');
        exit;
    }

    require_once __DIR__ . '/config/database.php';

    $stmt = $pdo->prepare(
        'SELECT id, name, email, created_at
        FROM users
        WHERE id = :id'
    );
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: auth/login.php');
        exit;
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $error = 'Name and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid email address.';
        } else {
            $check = $pdo->prepare(
                'SELECT id FROM users
                WHERE email = :email AND id != :id'
            );
            $check->execute([
                'email' => $email,
                'id' => $_SESSION['user_id']
            ]);

            if ($check->fetch()) {
                $error = 'This email is already used by another admin.';
            } else {
                $update = $pdo->prepare(
                    'UPDATE users
                    SET name = :name, email = :email
                    WHERE id = :id'
                );

                $update->execute([
                    'name' => $name,
                    'email' => $email,
                    'id' => $_SESSION['user_id']
                ]);

                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                $user['name'] = $name;
                $user['email'] = $email;
                $success = 'Profile updated successfully.';
            }
        }
    }

    $pageTitle = 'My Profile';
    require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        <div class="card border-0 shadow-lg rounded-5 position-relative mb-4">

            <div class="position-absolute top-0 start-50 translate-middle">
                <div class="bg-dark text-white rounded-circle shadow d-inline-flex
                            align-items-center justify-content-center"
                     style="width: 90px; height: 90px; font-size: 34px;">
                    <?= htmlspecialchars(strtoupper(substr($user['name'], 0, 1))) ?>
                </div>
            </div>

            <div class="card-body p-4 pt-5 mt-4">

                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mt-3 mb-1">
                        <?= htmlspecialchars($user['name']) ?>
                    </h1>

                    <span class="badge bg-dark rounded-pill px-3 py-2">
                        👤 Administrator
                    </span>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>⚠️ <?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <div>✅ <?= htmlspecialchars($success) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="name" class="form-control"
                               placeholder="Full Name" value="<?= htmlspecialchars($user['name']) ?>" required>
                        <label for="name">Full Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        <label for="email">Email Address</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="text" id="created_at" class="form-control"
                               placeholder="Account Created" value="<?= htmlspecialchars($user['created_at']) ?>" readonly>
                        <label for="created_at">Account Created</label>
                    </div>

                    <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm">
                        Update Profile
                    </button>
                </form>

                <a href="auth/change_password.php"
                   class="btn btn-outline-dark w-100 rounded-pill mt-3">
                    🔑 Change Password
                </a>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
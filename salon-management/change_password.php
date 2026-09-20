<?php
    declare(strict_types=1);

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/includes/auth.php';

    requireLogin();

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (
            $currentPassword === '' ||
            $newPassword === '' ||
            $confirmPassword === ''
        ) {
            $errors[] = 'Please complete all password fields.';
        }

        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters long.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirmation do not match.';
        }

        if (empty($errors)) {
            $getPassword = $pdo->prepare(
                'SELECT password
                FROM users
                WHERE id = :id'
            );

            $getPassword->execute([
                'id' => (int) $_SESSION['user_id']
            ]);

            $user = $getPassword->fetch();

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $errors[] = 'Your current password is incorrect.';
            } else {
                $updatePassword = $pdo->prepare(
                    'UPDATE users
                    SET password = :password
                    WHERE id = :id'
                );

                $updatePassword->execute([
                    'password' => password_hash(
                        $newPassword,
                        PASSWORD_DEFAULT
                    ),
                    'id' => (int) $_SESSION['user_id']
                ]);

                session_regenerate_id(true);

                $_SESSION['success_message'] =
                    'Password changed successfully.';

                redirect('/change_password.php');
            }
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

$pageTitle = 'Change Password';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <h1 class="h3 mb-1">Change Password</h1>

                    <p class="text-muted mb-4">
                        Use a strong password with at least 6 characters.
                    </p>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <?= e($successMessage) ?>
                        </div>
                    <?php endif; ?>

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
                            <label for="current_password" class="form-label">
                                Current Password *
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="current_password"
                                name="current_password"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                New Password *
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="new_password"
                                name="new_password"
                                minlength="6"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                Confirm New Password *
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="6"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Change Password
                        </button>

                        <a
                            href="<?= BASE_URL ?>/profile.php"
                            class="btn btn-outline-secondary"
                        >
                            Back to Profile
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
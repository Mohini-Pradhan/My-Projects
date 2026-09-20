<?php
    session_start();

    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    require_once __DIR__ . '/../config/database.php';

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $error = 'All password fields are required.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'New password must contain at least 6 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New password and confirm password do not match.';
        } else {
            $stmt = $pdo->prepare(
                'SELECT password FROM users WHERE id = :id'
            );
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $error = 'Current password is incorrect.';
            } else {
                $update = $pdo->prepare(
                    'UPDATE users SET password = :password WHERE id = :id'
                );

                $update->execute([
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                    'id' => $_SESSION['user_id']
                ]);

                $success = 'Password updated successfully.';
            }
        }
    }

$pageTitle = 'Change Password';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 550px;">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center mb-4">
            <span class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; font-size:20px;">
                🔑
            </span>
            <div>
                <h1 class="h3 fw-bold mb-0">Change Password</h1>
                <p class="text-muted mb-0">Keep your account secure</p>
            </div>
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
                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Current Password" required>
                <label for="current_password">Current Password</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" required>
                <label for="new_password">New Password</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                <label for="confirm_password">Confirm New Password</label>
            </div>

            <button class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm">Update Password</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
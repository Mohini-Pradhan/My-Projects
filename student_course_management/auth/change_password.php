<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $errors = [];
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $current_password = $_POST["current_password"] ?? "";
        $new_password = $_POST["new_password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION["user_id"]]);
        $user = $stmt->fetch();

        if (empty($current_password)) $errors[] = "Current password is required.";
        if (empty($new_password)) $errors[] = "New password is required.";
        if (strlen($new_password) < 6) $errors[] = "New password must be at least 6 characters.";
        if ($new_password !== $confirm_password) $errors[] = "New passwords do not match.";

        if (empty($errors) && !password_verify($current_password, $user["password"])) {
            $errors[] = "Current password is incorrect.";
        }

        if (empty($errors) && password_verify($new_password, $user["password"])) {
            $errors[] = "New password must be different from the current password.";
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $_SESSION["user_id"]]);

            $success = "Password changed successfully.";
        }
    }

    require_once "../includes/header.php";
?>

    <h2 class="mb-4">Change Password</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 shadow-sm rounded" style="max-width: 500px;">
        <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>

<?php require_once "../includes/footer.php"; ?>
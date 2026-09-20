<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $errors = [];
    $success = "";
    $activeTab = "profile";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    $user = $stmt->fetch();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST["update_profile"])) {
            $activeTab = "profile";
            $name = trim($_POST["name"]);
            $email = trim($_POST["email"]);

            if (empty($name)) $errors[] = "Name is required.";
            if (empty($email)) $errors[] = "Email is required.";

            if (empty($errors)) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $_SESSION["user_id"]]);
                if ($stmt->fetch()) {
                    $errors[] = "Email is already used by another account.";
                }
            }

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $_SESSION["user_id"]]);

                $_SESSION["user_name"] = $name;
                $success = "Profile updated successfully.";

                $user["name"] = $name;
                $user["email"] = $email;
            } else {
                $user["name"] = $name;
                $user["email"] = $email;
            }
        }

        if (isset($_POST["update_password"])) {
            $activeTab = "password";
            $current_password = $_POST["current_password"] ?? "";
            $new_password = $_POST["new_password"] ?? "";
            $confirm_password = $_POST["confirm_password"] ?? "";

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
    }

    require_once '../includes/header.php';
?>

    <div class="profile-card card mb-4">
        <div class="profile-header text-center">
            <div class="profile-avatar">
                <?= strtoupper(substr($user["name"], 0, 1)) ?>
            </div>
            <h4 class="mb-0"><?= htmlspecialchars($user["name"]) ?></h4>
            <small><?= htmlspecialchars($user["email"]) ?></small>
        </div>

        <div class="card-body p-4">

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

            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'profile' ? 'active' : '' ?>" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab">
                        Profile Info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $activeTab === 'password' ? 'active' : '' ?>" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab">
                        Change Password
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade <?= $activeTab === 'profile' ? 'show active' : '' ?>" id="profile-pane" role="tabpanel">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <div class="tab-pane fade <?= $activeTab === 'password' ? 'show active' : '' ?>" id="password-pane" role="tabpanel">
                    <form method="POST">
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
                        <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

<?php require_once "../includes/footer.php"; ?>
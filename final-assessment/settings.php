<?php
    require_once "auth.php";

if (empty($_SESSION["settings_csrf"])) {
    $_SESSION["settings_csrf"] = bin2hex(random_bytes(32));
}

function settings_e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$message = "";
$messageType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["settings_csrf"], $token)) {
        $message = "Invalid request. Please refresh and try again.";
        $messageType = "danger";
    } else {
        $action = $_POST["action"] ?? "";

        if ($action === "profile") {
            $name = trim($_POST["display_name"] ?? "");
            $email = trim($_POST["email"] ?? "");

            if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Enter a name and a valid email address.";
                $messageType = "danger";
            } else {
                $photoPath = $currentAdmin["photo"];

                if (
                    isset($_FILES["photo"]) &&
                    $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE
                ) {
                    $file = $_FILES["photo"];

                    if ($file["error"] !== UPLOAD_ERR_OK || $file["size"] > 2 * 1024 * 1024) {
                        $message = "Photo upload failed. Choose an image under 2 MB.";
                        $messageType = "danger";
                    } else {
                        $imageInfo = @getimagesize($file["tmp_name"]);
                        $mime = $imageInfo["mime"] ?? "";

                        $allowedTypes = [
                            "image/jpeg" => "jpg",
                            "image/png" => "png",
                            "image/webp" => "webp"
                        ];

                        if (!isset($allowedTypes[$mime])) {
                            $message = "Use a JPG, PNG, or WEBP image.";
                            $messageType = "danger";
                        } else {
                            $uploadDir = __DIR__ . "/assets/uploads";

                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }

                            $fileName = "admin_" . $currentAdmin["id"] . "_"
                                . bin2hex(random_bytes(8)) . "."
                                . $allowedTypes[$mime];

                            if (
                                !move_uploaded_file(
                                    $file["tmp_name"],
                                    $uploadDir . "/" . $fileName
                                )
                            ) {
                                $message = "Could not save the uploaded photo.";
                                $messageType = "danger";
                            } else {
                                $photoPath = "assets/uploads/" . $fileName;
                            }
                        }
                    }
                }

                if ($message === "") {
                    $stmt = $pdo->prepare(
                        "UPDATE admins
                         SET display_name = :name, email = :email, photo = :photo
                         WHERE id = :id"
                    );
                    $stmt->execute([
                        ":name" => $name,
                        ":email" => $email,
                        ":photo" => $photoPath,
                        ":id" => $currentAdmin["id"]
                    ]);

                    $message = "Profile updated successfully.";
                }
            }
        } elseif ($action === "password") {
            $currentPassword = $_POST["current_password"] ?? "";
            $newPassword = $_POST["new_password"] ?? "";
            $confirmPassword = $_POST["confirm_password"] ?? "";

            $stmt = $pdo->prepare(
                "SELECT password_hash FROM admins WHERE id = :id"
            );
            $stmt->execute([":id" => $currentAdmin["id"]]);
            $passwordHash = $stmt->fetchColumn();

            if (!password_verify($currentPassword, $passwordHash)) {
                $message = "Current password is incorrect.";
                $messageType = "danger";
            } elseif (strlen($newPassword) < 6) {
                $message = "New password must be at least 6 characters.";
                $messageType = "danger";
            } elseif ($newPassword !== $confirmPassword) {
                $message = "New password and confirmation do not match.";
                $messageType = "danger";
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE admins SET password_hash = :hash WHERE id = :id"
                );
                $stmt->execute([
                    ":hash" => password_hash($newPassword, PASSWORD_DEFAULT),
                    ":id" => $currentAdmin["id"]
                ]);

                $message = "Password changed successfully.";
            }
        } elseif ($action === "theme") {
            $theme = $_POST["theme"] ?? "light";

            if (!in_array($theme, ["light", "dark"], true)) {
                $message = "Choose a valid theme.";
                $messageType = "danger";
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE admins SET theme = :theme WHERE id = :id"
                );
                $stmt->execute([
                    ":theme" => $theme,
                    ":id" => $currentAdmin["id"]
                ]);

                $message = "Theme preference saved.";
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id");
$stmt->execute([":id" => $currentAdmin["id"]]);
$currentAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en" data-bs-theme="<?= settings_e($currentAdmin["theme"]) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | PHP Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-body-tertiary">
    <main class="container py-4" style="max-width: 850px">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Settings</h1>
                <p class="text-secondary mb-0">Manage your admin account and preferences.</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary">Back to dashboard</a>
        </div>

        <?php if ($message !== ""): ?>
            <div class="alert alert-<?= settings_e($messageType) ?>">
                <?= settings_e($message) ?>
            </div>
        <?php endif; ?>

        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Admin profile</h2>

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= settings_e($_SESSION["settings_csrf"]) ?>">
                    <input type="hidden" name="action" value="profile">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="<?= settings_e($currentAdmin["photo"]) ?>" alt="Admin profile photo" width="72"
                            height="72" class="rounded-circle border" style="object-fit: cover"
                            onerror="this.src='assets/admin.jpg'">
                        <div>
                            <label for="photo" class="form-label">Change photo</label>
                            <input type="file" id="photo" name="photo" class="form-control"
                                accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG, or WEBP; maximum 2 MB.</div>
                        </div>
                    </div>

                    <label class="form-label" for="display_name">Display name</label>
                    <input class="form-control mb-3" id="display_name" name="display_name"
                        value="<?= settings_e($currentAdmin["display_name"]) ?>" required>

                    <label class="form-label" for="email">Email</label>
                    <input class="form-control mb-3" type="email" id="email" name="email"
                        value="<?= settings_e($currentAdmin["email"]) ?>" required>

                    <button class="btn btn-primary">Save profile</button>
                </form>
            </div>
        </section>

        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Change password</h2>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= settings_e($_SESSION["settings_csrf"]) ?>">
                    <input type="hidden" name="action" value="password">

                    <label class="form-label" for="current_password">Current password</label>
                    <input class="form-control mb-3" type="password" id="current_password" name="current_password"
                        required>

                    <label class="form-label" for="new_password">New password</label>
                    <input class="form-control mb-3" type="password" id="new_password" name="new_password" minlength="6"
                        required>

                    <label class="form-label" for="confirm_password">Confirm new password</label>
                    <input class="form-control mb-3" type="password" id="confirm_password" name="confirm_password"
                        minlength="6" required>

                    <button class="btn btn-primary">Update password</button>
                </form>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Appearance</h2>

                <form method="post" class="d-flex align-items-center gap-3">
                    <input type="hidden" name="csrf_token" value="<?= settings_e($_SESSION["settings_csrf"]) ?>">
                    <input type="hidden" name="action" value="theme">

                    <label for="theme" class="form-label mb-0">Theme</label>
                    <select class="form-select" id="theme" name="theme" style="max-width: 180px">
                        <option value="light" <?= $currentAdmin["theme"] === "light" ? "selected" : "" ?>>
                            Light
                        </option>
                        <option value="dark" <?= $currentAdmin["theme"] === "dark" ? "selected" : "" ?>>
                            Dark
                        </option>
                    </select>
                    <button class="btn btn-primary">Save theme</button>
                </form>
            </div>
        </section>

    </main>
</body>

</html>
<?php
    require_once "config/db.php";

$adminExists = (int) $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn() > 0;
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$adminExists) {
    $username = trim($_POST["username"] ?? "");
    $displayName = trim($_POST["display_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $username === "" || $displayName === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6
    ) {
        $message = "Enter all details and use a password with at least 6 characters.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO admins (username, display_name, email, password_hash)
             VALUES (:username, :display_name, :email, :password_hash)"
        );

        $stmt->execute([
            ":username" => $username,
            ":display_name" => $displayName,
            ":email" => $email,
            ":password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);

        header("Location: login.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 520px;">
            <div class="card-body p-4">
                <h1 class="h3 mb-2">Create admin account</h1>

                <?php if ($adminExists): ?>
                    <div class="alert alert-info">
                        An admin account already exists. <a href="login.php">Go to login</a>.
                    </div>
                <?php else: ?>
                    <p class="text-secondary">Set up your project administrator.</p>

                    <?php if ($message): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <label class="form-label">Username</label>
                        <input name="username" class="form-control mb-3" required>

                        <label class="form-label">Display name</label>
                        <input name="display_name" class="form-control mb-3" required>

                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control mb-3" required>

                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control mb-3" minlength="6" required>

                        <button class="btn btn-primary w-100">Create admin</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>
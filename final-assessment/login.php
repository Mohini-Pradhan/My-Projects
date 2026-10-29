<?php
    session_start();
    require_once "config/db.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT id, password_hash
         FROM admins
         WHERE username = :username
         LIMIT 1"
    );
    $stmt->execute([":username" => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin["password_hash"])) {
        session_regenerate_id(true);
        $_SESSION["admin_id"] = (int) $admin["id"];

        header("Location: index.php");
        exit;
    }

    $error = "Username or password is incorrect.";
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | PHP Practical Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 440px;">
            <div class="card-body p-4">
                <h1 class="h3 mb-2">Admin login</h1>
                <p class="text-secondary">Sign in to manage your project.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" name="username" class="form-control mb-3" autocomplete="username" required>

                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" class="form-control mb-4"
                        autocomplete="current-password" required>

                    <button class="btn btn-primary w-100">Sign in</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
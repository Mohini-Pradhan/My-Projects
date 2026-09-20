<?php
include "../config/db.php";
include "../includes/auth.php";

$message = "";
$error = "";

if (isset($_POST["update_profile"])) {

    $user_id = $_SESSION["user_id"];
    $email = trim($_POST["email"]);
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    $query = mysqli_prepare(
        $conn,
        "SELECT password FROM users WHERE id = ?"
    );

    mysqli_stmt_bind_param($query, "i", $user_id);
    mysqli_stmt_execute($query);

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($query));

    if (!password_verify($current_password, $user["password"])) {
        $error = "Current password is incorrect.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";

    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters.";

    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";

    } else {

        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );

        mysqli_stmt_bind_param($check, "si", $email, $user_id);
        mysqli_stmt_execute($check);

        if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
            $error = "This email is already in use.";

        } else {

            $password = !empty($new_password)
                ? password_hash($new_password, PASSWORD_DEFAULT)
                : $user["password"];

            $update = mysqli_prepare(
                $conn,
                "UPDATE users SET email = ?, password = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param($update, "ssi", $email, $password, $user_id);

            if (mysqli_stmt_execute($update)) {

            $_SESSION = [];
            session_destroy();

            header("Location: login.php?success=profile_updated");
            exit();
        }
        }
    }
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-5" style="max-width: 650px">
    <div class="card shadow-sm">
        <div class="card-body p-4">

            <h2 class="mb-4">Admin Profile</h2>

            <?php if ($message) { ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php } ?>

            <?php if ($error) { ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">New Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_SESSION["email"]) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>

                <button name="update_profile" class="btn btn-primary">
                    Update Profile
                </button>
            </form>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
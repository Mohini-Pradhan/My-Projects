<?php
session_start();

include "../config/db.php";

$error = "";

if (isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";

    } else {

        $query = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);

        $result = mysqli_stmt_get_result($query);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["email"] = $user["email"];

            header("Location: ../dashboard.php");
            exit();

        } else {
            $error = "Incorrect email or password.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h2 class="text-center mb-4">Admin Login</h2>

                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Email</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>

                            <input type="password"
                                   name="password"
                                   class="form-control">
                        </div>

                        <button type="submit"
                                name="login"
                                class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
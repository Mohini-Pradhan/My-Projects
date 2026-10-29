<?php
    require_once "auth.php";
    require_once "classes/user.php";

if (isset($_POST["set_cookie"])) {
    setcookie(
        "username",
        "Mohini",
        time() + 3600,
        "/"
    );

    header("Location: cookie.php");
    exit;
}

require_once "includes/header.php";
?>

<div class="card p-4">
    <h2>Question 15: PHP Cookies</h2>

    <form method="POST">
        <button type="submit" name="set_cookie" class="btn btn-primary">
            Set Cookie
        </button>
    </form>

    <hr>

    <h5>Cookie Value:</h5>

    <?php
    if (isset($_COOKIE["username"])) {
        echo htmlspecialchars($_COOKIE["username"]);
    } else {
        echo "Cookie is not set yet. Click Set Cookie.";
    }
    ?>
</div>

<?php
require_once "includes/footer.php";
?>
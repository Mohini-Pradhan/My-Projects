<?php
    session_start();

    include "config/db.php";
    include "includes/header.php";
    ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 text-center">

            <h1 class="display-5 fw-bold">
                Employee Department Management System
            </h1>

            <p class="lead text-muted mt-3">
                Securely manage departments, employees, reports, and salary statistics.
            </p>

            <div class="mt-4">

                <?php if (isset($_SESSION["user_id"])) { ?>

                    <a href="dashboard.php" class="btn btn-primary btn-lg">
                        Go to Dashboard
                    </a>

                    <a href="auth/logout.php" class="btn btn-outline-danger btn-lg">
                        Logout
                    </a>

                <?php } else { ?>

                    <a href="auth/login.php" class="btn btn-primary btn-lg">
                        Admin Login
                    </a>

                <?php } ?>

            </div>

        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
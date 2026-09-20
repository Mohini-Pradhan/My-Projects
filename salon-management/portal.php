<?php
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Choose Portal | <?= APP_NAME ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #fff4f8, #f8f9fa, #f6e2eb);
        }

        .portal-card {
            border-radius: 1.25rem;
            transition: transform 0.2s ease;
        }

        .portal-card:hover {
            transform: translateY(-6px);
        }

        .portal-icon {
            width: 75px;
            height: 75px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            background: linear-gradient(135deg, #4a0f2a, #c12f68);
            border-radius: 50%;
            font-size: 2rem;
        }
    </style>
</head>

<body>

<main class="container py-5">

    <div class="text-center mb-5">
        <div class="portal-icon mb-3">✦</div>

        <h1 class="display-6 fw-bold">
            <?= APP_NAME ?>
        </h1>

        <p class="lead text-muted mb-0">
            Please select how you want to continue.
        </p>
    </div>

    <div class="row justify-content-center g-4">

        <div class="col-md-5">
            <div class="card portal-card border-0 shadow h-100">
                <div class="card-body p-4 p-lg-5 text-center">

                    <div class="portal-icon mb-4">✂</div>

                    <h2 class="h3">Customer</h2>

                    <p class="text-muted mb-4">
                        Browse services, create an account, book appointments,
                        make payments, and check your booking details.
                    </p>

                    <a href="<?= BASE_URL ?>/customer/index.php" class="btn btn-primary btn-lg w-100" style="background-color:#8f1d4c;">
                        Continue as Customer
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card portal-card border-0 shadow h-100">
                <div class="card-body p-4 p-lg-5 text-center">

                    <div class="portal-icon mb-4">📊</div>

                    <h2 class="h3">Salon Admin</h2>

                    <p class="text-muted mb-4">
                        Manage salon services, beauticians, customers,
                        appointments, payments, reports, and revenue.
                    </p>

                    <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-primary btn-lg w-100" style="background-color:#8f1d4c;">
                        Admin Login
                    </a>

                </div>
            </div>
        </div>

    </div>

</main>

</body>
</html>
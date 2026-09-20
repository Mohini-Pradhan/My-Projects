<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = $pageTitle ?? APP_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        .salon-navbar {
            background: linear-gradient(135deg, #4a0f2a, #8f1d4c, #c12f68);
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 50%;
            font-size: 1.15rem;
        }

        .salon-navbar .nav-link {
            color: rgba(255, 255, 255, 0.82);
            font-weight: 500;
            padding: 0.55rem 0.75rem;
            border-radius: 0.45rem;
        }

        .salon-navbar .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.14);
        }

        .user-name {
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark salon-navbar shadow-sm sticky-top">
    <div class="container">

        <a
            class="navbar-brand d-flex align-items-center gap-2 fw-bold"
            href="<?= BASE_URL ?>/customer/index.php"
        >
            <span class="brand-icon">✦</span>

            <span>
                <?= APP_NAME ?>

                <small class="d-block fw-normal opacity-75" style="font-size: 0.68rem;">
                    Salon & Beauty Studio
                </small>
            </span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#customerNavigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="customerNavigation">

            <ul class="navbar-nav mx-auto gap-lg-1">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= BASE_URL ?>/customer/services.php"
                    >
                        Services
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/customer/about.php">
                        About Us
                    </a>
                </li>

                <?php if (isCustomer()): ?>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>/customer/booking.php"
                        >
                            Book Appointment
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                        >
                            My Account
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="<?= BASE_URL ?>/customer/appointments.php"
                                >
                                    My Bookings
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="<?= BASE_URL ?>/customer/profile.php"
                                >
                                    My Profile
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a
                                    class="dropdown-item text-danger"
                                    href="<?= BASE_URL ?>/auth/logout.php"
                                >
                                    Logout
                                </a>
                            </li>

                        </ul>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>/customer/register.php"
                        >
                            Sign Up
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>/customer/login.php"
                        >
                            Customer Login
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

            <?php if (isCustomer()): ?>
                <span class="user-name mt-3 mt-lg-0">
                    Hi, <?= e($_SESSION['user_name'] ?? 'Customer') ?>
                </span>
            <?php endif; ?>

        </div>
    </div>
</nav>
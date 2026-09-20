<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

$pageTitle = $pageTitle ?? APP_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?> | <?= APP_NAME ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        .admin-sidebar {
            width: 260px;
            min-height: 100vh;
            color: #ffffff;
            background: linear-gradient(180deg, #4a0f2a, #8f1d4c, #c12f68);
        }

        .admin-brand {
            color: #ffffff;
            text-decoration: none;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 50%;
        }

        .admin-sidebar .nav-link {
            margin-bottom: 0.25rem;
            padding: 0.7rem 0.85rem;
            color: rgba(255, 255, 255, 0.84);
            border-radius: 0.55rem;
        }

        .admin-sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.14);
        }

        .admin-content {
            min-width: 0;
            background: #f8f9fa;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>

<body>

<div class="d-lg-flex">

    <aside class="admin-sidebar p-3">

        <a
            href="<?= BASE_URL ?>/index.php"
            class="admin-brand d-flex align-items-center gap-2 fw-bold mb-4"
        >
            <span class="brand-icon">✦</span>

            <span>
                <?= APP_NAME ?>

                <small class="d-block fw-normal opacity-75">
                    Admin Panel
                </small>
            </span>
        </a>

        <nav class="nav flex-column">

            <a class="nav-link" href="<?= BASE_URL ?>/index.php">
                <i class="bi bi-grid me-2"></i> Dashboard
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/customers/index.php">
                <i class="bi bi-people me-2"></i> Customers
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/services/index.php">
                <i class="bi bi-scissors me-2"></i> Services
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/beauticians/index.php">
                <i class="bi bi-person-heart me-2"></i> Beauticians
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/beauticians/leave.php">
                <i class="bi bi-calendar-x me-2"></i> Beautician Leave
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/appointments/index.php">
                <i class="bi bi-calendar-check me-2"></i> Appointments
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/payments/index.php">
                <i class="bi bi-cash-stack me-2"></i> Payments
            </a>

            <a class="nav-link" href="<?= BASE_URL ?>/reports/index.php">
                <i class="bi bi-bar-chart-line me-2"></i> Reports
            </a>

        </nav>

        <hr class="border-light opacity-25">

        <div class="dropdown">
            <button
                class="btn btn-outline-light w-100 text-start dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
            >
                <i class="bi bi-person-circle me-2"></i>
                <?= e($_SESSION['user_name'] ?? 'Admin') ?>
            </button>

            <ul class="dropdown-menu w-100">
                <li>
                    <a
                        class="dropdown-item"
                        href="<?= BASE_URL ?>/profile.php"
                    >
                        My Profile
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item"
                        href="<?= BASE_URL ?>/change_password.php"
                    >
                        Change Password
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
        </div>

    </aside>

    <main class="admin-content flex-grow-1">
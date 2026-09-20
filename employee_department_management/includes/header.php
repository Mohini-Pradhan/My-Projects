<?php
    $pageTitle = $pageTitle ?? 'Employee Management System';

    $baseUrl = "/my_ws_php_classes/Additional-Project-Practice-Scenarios/employee_department_management";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient shadow-lg">
        <div class="container">
            <a href="<?= $baseUrl ?>/index.php" class="navbar-brand fw-bold d-flex align-items-center">
                <span class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:36px; height:36px;">
                    🏢
                </span>
                Employee Management
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <ul class="navbar-nav ms-auto align-items-lg-center">

                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php" class="nav-link px-lg-3 rounded-pill">
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/departments/index.php" class="nav-link px-lg-3 rounded-pill">
                                Departments
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/employees/index.php" class="nav-link px-lg-3 rounded-pill">
                                Employees
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/reports/index.php" class="nav-link px-lg-3 rounded-pill">
                                Reports
                            </a>
                        </li>

                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle fw-semibold d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:32px; height:32px;">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                                </span>
                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'Profile') ?>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg p-2 rounded-4 mt-2"
                                style="min-width: 220px;">
                                <li>
                                    <a class="dropdown-item py-2 fw-semibold rounded-3" href="<?= $baseUrl ?>/profile.php">
                                        👤 My Profile
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item py-2 fw-semibold rounded-3" href="<?= $baseUrl ?>/auth/change_password.php">
                                        🔑 Change Password
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item py-2 fw-semibold text-warning rounded-3"
                                       href="<?= $baseUrl ?>/auth/logout.php">
                                        🚪 Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container py-5">
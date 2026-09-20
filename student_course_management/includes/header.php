<?php
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Student Course Management System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">
                    <img src="<?= BASE_URL ?>/includes/logo.png" alt="Logo" height="70" class="me-2">
                    <span class="text-primary">S</span>CMS <span class="text-primary">I</span>NSTITUTE
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav me-auto mb-2">
                        <li class="nav-item ">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php">Dashboard</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Manage
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/students/list.php">Students</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/courses/list.php">Courses</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/enrollments/list.php">Enrollments</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/reports/index.php">Reports</a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="nav-avatar me-2">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                                </span>
                                <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/auth/profile.php">My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <div class="container-fluid mt-4 px-4">

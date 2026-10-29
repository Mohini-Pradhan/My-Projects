<?php
    require_once "auth.php";        

$totalUsers = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Error fetching user count: " . $e->getMessage());
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>
<!doctype html>
<html lang="en" data-bs-theme="<?= htmlspecialchars($currentAdmin["theme"]) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add User | PHP Practical Lab</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: var(--bs-body-bg);
            color: var(--bs-body-color);
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #222a3d;
        }

        .sidebar a {
            color: #cbd2df;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 8px;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            background: #343e56;
        }

        .main-content {
            min-width: 0;
        }

        .topbar,
        .content-card {
            background: var(--bs-tertiary-bg);
        }

        .content-card {
            border: 1px solid #e9edf3;
            border-radius: 14px;
        }

        .form-control {
            border-color: #dce2eb;
            padding: 12px 14px;
        }

        .form-control:focus {
            border-color: #86a8ff;
            box-shadow: 0 0 0 .2rem rgba(49, 89, 183, .12);
        }

        .icon-tile {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #eaf0ff;
            color: #3159b7;
            font-size: 20px;
        }

        @media (max-width: 767px) {
            .sidebar {
                width: 70px;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .sidebar .menu-label,
            .sidebar .brand-name {
                display: none;
            }

            .sidebar a {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">

        <aside class="sidebar p-3">
            <a href="index.php" class="text-white fw-bold fs-5 mb-4">
                <i class="bi bi-grid-1x2-fill"></i>
                <span class="brand-name">PHP Admin</span>
            </a>

            <div class="small text-uppercase text-secondary px-3 mb-2 menu-label">
                Workspace
            </div>

            <a href="index.php" class="active">
                <i class="bi bi-person-plus"></i>
                <span class="menu-label">Add User</span>
            </a>
            <a href="users.php">
                <i class="bi bi-people"></i>
                <span class="menu-label">Users List</span>
            </a>
            <a href="demo.php">
                <i class="bi bi-code-slash"></i>
                <span class="menu-label">PHP Questions</span>
            </a>
            <a href="cookie.php">
                <i class="bi bi-cookie"></i>
                <span class="menu-label">Cookie</span>
            </a>

            <hr class="border-secondary">

            <div class="small text-uppercase text-secondary px-3 mb-2 menu-label">
                Account
            </div>
            <a href="settings.php">
                <i class="bi bi-gear"></i>
                <span class="menu-label">Settings</span>
            </a>

            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span class="menu-label">Log out</span>
            </a>
        </aside>

        <div class="main-content flex-grow-1">
            <header class="topbar border-bottom px-4 py-3 d-flex
                       justify-content-between align-items-center gap-3">
                <form method="GET" action="users.php" class="d-flex" role="search">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input class="form-control" type="search" name="search" placeholder="Search users">
                    </div>
                </form>

                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell text-secondary"></i>
                    <img src="<?= htmlspecialchars($currentAdmin["photo"]) ?>"
                        alt="Admin profile photo"
                        width="36"
                        height="36"
                        class="rounded-circle"
                        style="object-fit: cover">

                    <span class="small fw-semibold d-none d-sm-inline">
                        <?= htmlspecialchars($currentAdmin["display_name"]) ?>
                    </span>
                </div>
            </header>

            <main class="p-3 p-md-4">
                <div class="mb-4">
                    <div class="small text-secondary mb-2">
                        <i class="bi bi-house"></i> &nbsp; Workspace &nbsp;›&nbsp; Add User
                    </div>
                    <span class="badge text-bg-primary mb-2">USER MANAGEMENT</span>
                    <h1 class="h3 fw-bold mb-1">Add a new user</h1>
                    <p class="text-secondary mb-0">
                        Create a user record and keep your database up to date.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <section class="content-card shadow-sm">
                            <div class="p-4 p-md-5">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="icon-tile">
                                        <i class="bi bi-person-vcard"></i>
                                    </div>
                                    <div>
                                        <h2 class="h5 fw-bold mb-1">User details</h2>
                                        <p class="text-secondary small mb-0">
                                            Fill in the information below.
                                        </p>
                                    </div>
                                </div>

                                <div id="message" aria-live="polite"></div>

                                <form id="userForm" method="post" novalidate>
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-semibold">
                                            Full name
                                        </label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Enter Your Name" autocomplete="name" required>
                                        <div class="form-text">
                                            Enter the user's first and last name.
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold">
                                            Email address
                                        </label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="Enter Youe Email" autocomplete="email" required>
                                        <div class="form-text">
                                            Use a valid email address.
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" id="saveButton" class="btn btn-primary px-4">
                                            <i class="bi bi-check2-circle me-1"></i>
                                            Save user
                                        </button>

                                        <button type="reset" id="clearButton" class="btn btn-outline-secondary px-4">
                                            Clear form
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-4">
                        <section class="content-card shadow-sm p-4 mb-4">
                            <div class="small text-secondary mb-2">DATABASE OVERVIEW</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-secondary small">Total users</div>
                                    <div class="display-6 fw-bold"><?= $totalUsers ?></div>
                                </div>
                                <div class="icon-tile">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                            <a href="users.php" class="btn btn-light w-100 mt-3">
                                View users <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </section>

                        <section class="content-card shadow-sm p-4">
                            <h2 class="h6 fw-bold mb-3">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                Quick note
                            </h2>
                            <p class="small text-secondary mb-0">
                                The form checks the email format in the browser and
                                sends the user details to the PHP handler.
                            </p>
                        </section>
                    </div>
                </div>

                <footer class="text-secondary small mt-4">
                    © 2026 PHP Practical Lab
                </footer>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/script.js"></script>
</body>

</html>
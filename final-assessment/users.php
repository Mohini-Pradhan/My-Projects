<?php
    require_once "auth.php";
    require_once "classes/user.php";

$search = trim($_GET["search"] ?? "");
$searchA = isset($_GET["search_a"]);

if ($searchA) {
    $stmt = $pdo->prepare(
        "SELECT * FROM users
         WHERE name LIKE :name
         ORDER BY id DESC"
    );
    $stmt->execute([":name" => "A%"]);
} elseif ($search !== "") {
    $stmt = $pdo->prepare(
        "SELECT * FROM users
         WHERE name LIKE :search OR email LIKE :search
         ORDER BY id DESC"
    );
    $stmt->execute([":search" => "%" . $search . "%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
    $stmt->execute();
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function initials($name)
{
    $parts = preg_split('/\s+/', trim($name));
    $letters = "";

    foreach (array_slice($parts, 0, 2) as $part) {
        $letters .= mb_substr($part, 0, 1);
    }

    return mb_strtoupper($letters);
}
?>
<!doctype html>
<html lang="en" data-bs-theme="<?= htmlspecialchars($currentAdmin["theme"]) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users List | PHP Practical Lab</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fa;
            color: #202637;
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
            gap: 12px;
            align-items: center;
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
            background: #fff;
        }

        .content-card {
            border: 1px solid #e9edf3;
            border-radius: 12px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-grid;
            place-items: center;
            background: #e8efff;
            color: #3159b7;
            font-weight: 700;
            flex: 0 0 auto;
        }

        .table> :not(caption)>*>* {
            padding: 14px 12px;
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

            <a href="index.php">
                <i class="bi bi-speedometer2"></i><span class="menu-label">Dashboard</span>
            </a>
            <a href="users.php" class="active">
                <i class="bi bi-people"></i><span class="menu-label">Users List</span>
            </a>
            <a href="demo.php">
                <i class="bi bi-code-slash"></i><span class="menu-label">PHP Questions</span>
            </a>
            <a href="cookie.php">
                <i class="bi bi-cookie"></i><span class="menu-label">Cookie Demo</span>
            </a>

            <hr class="border-secondary">

            <div class="small text-uppercase text-secondary px-3 mb-2 menu-label">
                Account
            </div>
            <a href="#">
                <i class="bi bi-gear"></i><span class="menu-label">Settings</span>
            </a>

            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span class="menu-label">Log out</span>
            </a>
        </aside>

        <div class="main-content flex-grow-1">
            <header class="topbar border-bottom px-4 py-3 d-flex
                       justify-content-between align-items-center gap-3">
                <form method="GET" class="d-flex" role="search">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input class="form-control" type="search" name="search" value="<?= e($search) ?>"
                            placeholder="Search name or email">
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
                <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-3 mb-4">
                    <div>
                        <div class="small text-secondary mb-2">
                            <i class="bi bi-house"></i> &nbsp; Users &nbsp;›&nbsp; Users List
                        </div>
                        <h1 class="h3 fw-bold mb-1">Users List</h1>
                        <p class="text-secondary mb-0">
                            Manage and review users saved in your database.
                        </p>
                    </div>

                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New User
                    </a>
                </div>

                <section class="content-card shadow-sm p-3 p-md-4">
                    <div class="d-flex flex-wrap justify-content-between
                            align-items-center gap-3 mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="users.php?search_a=1" class="btn btn-outline-secondary btn-sm">
                                Names starting with A
                            </a>
                            <a href="users.php" class="btn btn-outline-secondary btn-sm">
                                Show all
                            </a>
                        </div>

                        <span class="small text-secondary">
                            <?= count($users) ?> user(s)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-secondary">
                                    <th style="width: 55px">#</th>
                                    <th>NAME</th>
                                    <th>DATE CREATED</th>
                                    <th>EMAIL</th>
                                    <th class="text-end">ID</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($users): ?>
                                    <?php foreach ($users as $index => $user): ?>
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox" aria-label="Select user">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="avatar">
                                                        <?= e(initials($user["name"])) ?>
                                                    </span>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            <?= e($user["name"]) ?>
                                                        </div>
                                                        <div class="small text-secondary">
                                                            <?= e($user["email"]) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-secondary">
                                                <?= e($user["created_at"] ?? "—") ?>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill text-bg-success-subtle
                                                     border border-success-subtle text-success">
                                                    <i class="bi bi-check-circle-fill"></i> Email
                                                </span>
                                            </td>
                                            <td class="text-end text-secondary">
                                                <?= (int) $user["id"] ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">
                                            No users found.
                                            <a href="index.php">Add your first user</a>.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center
                            border-top pt-3 mt-2">
                        <span class="small text-secondary">
                            Showing <?= count($users) ?> user(s)
                        </span>
                        <span class="small text-secondary">
                            Sorted by newest ID
                        </span>
                    </div>
                </section>

                <footer class="text-secondary small mt-4">
                    © 2026 PHP Practical Lab
                </footer>
            </main>
        </div>
    </div>
</body>

</html>
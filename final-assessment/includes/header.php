<!doctype html>
<html lang="en" data-bs-theme="<?= htmlspecialchars($currentAdmin["theme"]) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP Practical Lab | User Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: #f4f7fb;
            color: #1f2937;
        }

        .app-navbar {
            background: linear-gradient(120deg, #172554, #2563eb);
        }

        .brand-mark {
            display: inline-grid;
            place-items: center;
            width: 34px;
            height: 34px;
            margin-right: 8px;
            border-radius: 10px;
            background: #fff;
            color: #1d4ed8;
        }

        .app-main {
            min-height: calc(100vh - 150px);
        }

        .app-card {
            border: 0;
            border-radius: 18px;
        }

        .app-footer {
            color: #64748b;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <span class="brand-mark">P</span> PHP Practical Lab
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.php">Add User</a>
                    <a class="nav-link" href="users.php">All Users</a>
                    <a class="nav-link" href="demo.php">PHP Questions</a>
                    <a class="nav-link" href="cookie.php">Cookie Demo</a>
                    <a class="nav-link" href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container app-main py-5">
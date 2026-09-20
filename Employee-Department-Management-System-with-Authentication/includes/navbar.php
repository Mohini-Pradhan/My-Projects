<nav class="navbar navbar-expand-lg navbar-dark bg-info py-4">
    <div class="container">

        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>dashboard.php">
            Employee Department Management System
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>dashboard.php">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>department/index.php">
                        Departments
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>employee/index.php">
                        Employees
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>reports/department-report.php">
                        Reports
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>auth/profile.php">
                        Profile
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?= BASE_URL ?>auth/logout.php">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>
<footer class="bg-dark text-white mt-5">
    <div class="container py-5">

        <div class="row g-7">

            <div class="col-md-5">
                <h5 class="fw-bold">
                    Employee Management
                </h5>

                <p class="text-white-50 mb-0">
                    A secure Employee Department Management System built with
                    Core PHP, MySQL, Sessions, and Bootstrap 5.
                </p>
            </div>

            <div class="col-md-3">
                <h6 class="fw-bold">Quick Links</h6>

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>dashboard.php"
                           class="text-white-50 text-decoration-none">
                            Dashboard
                        </a>
                    </li>

                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>department/index.php"
                           class="text-white-50 text-decoration-none">
                            Departments
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>employee/index.php"
                           class="text-white-50 text-decoration-none">
                            Employees
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6 class="fw-bold">Reports</h6>

                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>reports/department-report.php"
                           class="text-white-50 text-decoration-none">
                            Department Report
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>reports/salary-report.php"
                           class="text-white-50 text-decoration-none">
                            Salary Report
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <p class="mb-0 text-white-50">
                © <?= date("Y") ?> Employee Department Management System
            </p>

            <p class="mb-0 text-white-50">Secure • Responsive • Easy to Manage</p>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
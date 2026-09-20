    </main>

    <footer class="bg-dark bg-gradient text-white pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row gy-4 align-items-center">

                <div class="col-md-4 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-2">
                        <span class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:36px; height:36px;">
                            🏢
                        </span>
                        <span class="fw-bold fs-5">Employee Management</span>
                    </div>
                    <p class="text-white-50 small mb-0">
                        Organized, efficient, and secure department management.
                    </p>
                </div>

                <div class="col-md-4 text-center">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item mx-2">
                            <a href="<?= $baseUrl ?>/index.php" class="link-light link-underline-opacity-0 small">
                                Dashboard
                            </a>
                        </li>
                        <li class="list-inline-item mx-2">
                            <a href="<?= $baseUrl ?>/employees/index.php" class="link-light link-underline-opacity-0 small">
                                Employees
                            </a>
                        </li>
                        <li class="list-inline-item mx-2">
                            <a href="<?= $baseUrl ?>/departments/index.php" class="link-light link-underline-opacity-0 small">
                                Departments
                            </a>
                        </li>
                        <li class="list-inline-item mx-2">
                            <a href="<?= $baseUrl ?>/reports/index.php" class="link-light link-underline-opacity-0 small">
                                Reports
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 text-center text-md-end">
                    <span class="badge bg-white text-dark rounded-pill px-3 py-2">
                        v1.0
                    </span>
                </div>

            </div>

            <hr class="border-secondary my-4">

            <p class="text-center text-white-50 small mb-0">
                © <?= date('Y') ?> Employee Department Management System. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
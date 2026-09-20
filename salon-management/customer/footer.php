<?php
require_once __DIR__ . '/../config/app.php';
?>

<footer class="text-white mt-5"
    style="background: linear-gradient(135deg, #4a0f2a, #8f1d4c, #c12f68);">
    
    <div class="container py-4">
        <div class="row gy-4">

            <div class="col-md-5">
                <h5 class="fw-bold"><?= APP_NAME ?></h5>

                <p class="text-white-50 mb-0">
                    Book salon services, manage appointments, and enjoy your beauty journey.
                </p>
            </div>

            <div class="col-6 col-md-3">
                <h6 class="text-uppercase small fw-bold">Quick Links</h6>

                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/customer/services.php"
                           class="text-white-50 text-decoration-none">
                            Services
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>/customer/appointments.php"
                               class="text-white-50 text-decoration-none">
                                My Bookings
                            </a>
                        </li>

                        <li>
                            <a href="<?= BASE_URL ?>/customer/profile.php"
                               class="text-white-50 text-decoration-none">
                                My Profile
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-6 col-md-4">
                <h6 class="text-uppercase small fw-bold">Account</h6>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <p class="text-white-50 mb-2">
                        Logged in as:
                        <strong class="text-white">
                            <?= e($_SESSION['user_name'] ?? 'Customer') ?>
                        </strong>
                    </p>

                    <a href="<?= BASE_URL ?>/auth/logout.php"
                       class="btn btn-outline-light btn-sm">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/customer/login.php"
                       class="btn btn-outline-light btn-sm">
                        Customer Login
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small text-white-50">
            <span>
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
            </span>

            <span>Your beauty, our care.</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
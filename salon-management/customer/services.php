<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$services = $pdo->query(
    'SELECT id, service_name, price, duration, description
     FROM services
     ORDER BY service_name ASC'
)->fetchAll();

$pageTitle = 'Our Services';
require_once __DIR__ . '/header.php';
?>

<section class="customer-hero">
    <div class="container py-5 text-center">
        <span class="text-uppercase fw-semibold small">
            Salon & Beauty Studio
        </span>

        <h1 class="display-5 fw-bold mt-2">Our Beauty Services</h1>

        <p class="lead mb-0">
            Find the perfect service and book your appointment online.
        </p>
    </div>
</section>

<main class="container py-5">

    <div class="row g-4">

        <?php if (empty($services)): ?>

            <div class="col-12">
                <div class="alert alert-light border text-center py-4">
                    Services will be added soon.
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($services as $service): ?>

                <div class="col-md-6 col-lg-4">
                    <article class="card service-card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex flex-column">

                            <div class="d-flex justify-content-between gap-3">
                                <h2 class="h5 mb-2">
                                    <?= e($service['service_name']) ?>
                                </h2>

                                <strong
                                    class="text-nowrap"
                                    style="color: #8f1d4c;"
                                >
                                    ₹<?= e(number_format((float) $service['price'], 2)) ?>
                                </strong>
                            </div>

                            <p class="text-muted small">
                                <?= e(
                                    $service['description']
                                    ?: 'A premium salon experience made for you.'
                                ) ?>
                            </p>

                            <p class="small mb-4">
                                ⏱ <?= (int) $service['duration'] ?> minutes
                            </p>

                            <a
                                href="<?= BASE_URL ?>/customer/booking.php?service=<?= (int) $service['id'] ?>"
                                class="btn btn-outline-primary w-100 mt-auto"
                            >
                                Book This Service
                            </a>

                        </div>
                    </article>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</main>

<?php require_once __DIR__ . "/footer.php"; ?>
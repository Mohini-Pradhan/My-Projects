<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    $services = $pdo->query(
        'SELECT id, service_name, price, duration, description
        FROM services
        ORDER BY service_name'
    )->fetchAll();

    $pageTitle = 'Beauty Services';

    require __DIR__ . "/header.php";

?>

<section class="customer-hero">
    <div class="container py-5">
        <div class="row align-items-center">

            <div class="col-lg-7">

                <span class="text-uppercase fw-semibold small">
                    Salon & Beauty Studio
                </span>

                <h1 class="display-4 fw-bold mt-2">
                    Feel beautiful.<br>
                    Book with ease.
                </h1>

                <p class="lead">
                    Discover our expert beauty services and reserve a time
                    that suits you.
                </p>

                <a href="<?= BASE_URL ?>/customer/booking.php" class="btn btn-primary btn-lg">
                    Book an Appointment
                </a>

            </div>

        </div>
    </div>
</section>


<main class="container py-5" id="services">

    <div class="text-center mb-4">

        <h2>Our Services</h2>

        <p class="text-muted">
            Professional care tailored for you.
        </p>

    </div>


    <div class="row g-4">

        <?php if (!$services): ?>

            <p class="text-center text-muted">
                Our services will be listed here soon.
            </p>

        <?php endif; ?>


        <?php foreach ($services as $service): ?>

            <div class="col-md-6 col-lg-4">

                <article class="card service-card h-100 border-0 shadow-sm">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between gap-2">

                            <h3 class="h5">
                                <?= e($service['service_name']) ?>
                            </h3>

                            <strong class="text-nowrap">
                                ₹<?= e(number_format((float) $service['price'], 2)) ?>
                            </strong>

                        </div>


                        <p class="text-muted small">
                            <?= e(
                                $service['description']
                                    ?: 'A premium salon experience.'
                            ) ?>
                        </p>


                        <p class="small mb-4">
                            ⌛ <?= (int) $service['duration'] ?> minutes
                        </p>


                        <a
                            href="<?= BASE_URL ?>/customer/booking.php?service=<?= (int) $service['id'] ?>"
                            class="btn btn-outline-primary w-100"
                        >
                            Book This Service
                        </a>

                    </div>

                </article>

            </div>

        <?php endforeach; ?>

    </div>

</main>


<?php require __DIR__ . "/footer.php"; ?>
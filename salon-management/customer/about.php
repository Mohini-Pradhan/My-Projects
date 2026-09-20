<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'About Us';

require_once __DIR__ . '/header.php';
?>

<section class="customer-hero">
    <div class="container py-5 text-center">
        <span class="text-uppercase fw-semibold small">
            Salon & Beauty Studio
        </span>

        <h1 class="display-5 fw-bold mt-2">
            About <?= APP_NAME ?>
        </h1>

        <p class="lead mb-0">
            Beauty, care, and confidence—made for you.
        </p>
    </div>
</section>

<main class="container py-5">

    <div class="row align-items-center g-5">

        <div class="col-lg-6">
            <h2 class="h2 mb-3">Your beauty is our priority</h2>

            <p class="text-muted">
                <?= APP_NAME ?> is dedicated to providing a relaxing and
                professional salon experience. Our experienced beauticians
                offer quality services designed around your personal style
                and beauty needs.
            </p>

            <p class="text-muted mb-0">
                From hair care and skincare to complete beauty treatments,
                we focus on comfort, hygiene, and customer satisfaction in
                every appointment.
            </p>
        </div>

        <div class="col-lg-6">
            <div
                class="card border-0 shadow-sm"
                style="background: #fff4f8;"
            >
                <div class="card-body p-4 p-md-5">

                    <h2 class="h4 mb-4">Why choose us?</h2>

                    <div class="mb-3">
                        <h3 class="h6 fw-bold">Professional Beauticians</h3>
                        <p class="text-muted mb-0">
                            Skilled beauty professionals who care about your experience.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h3 class="h6 fw-bold">Easy Online Booking</h3>
                        <p class="text-muted mb-0">
                            Book your preferred service, beautician, date, and time online.
                        </p>
                    </div>

                    <div>
                        <h3 class="h6 fw-bold">Flexible Appointment Management</h3>
                        <p class="text-muted mb-0">
                            Check your bookings, payment details, and cancel eligible appointments.
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mt-4 text-center">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="fs-2 mb-2">✦</div>
                    <h2 class="h5">Quality Care</h2>
                    <p class="text-muted mb-0">
                        Premium beauty care in a welcoming environment.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="fs-2 mb-2">⌚</div>
                    <h2 class="h5">Save Time</h2>
                    <p class="text-muted mb-0">
                        Select a convenient available slot before visiting.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="fs-2 mb-2">❤</div>
                    <h2 class="h5">Customer First</h2>
                    <p class="text-muted mb-0">
                        Your comfort and satisfaction always come first.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center mt-5">
        <a
            href="<?= BASE_URL ?>/customer/booking.php"
            class="btn btn-primary px-4"
        >
            Book an Appointment
        </a>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
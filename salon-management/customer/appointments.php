<?php

    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireCustomer();

    $query = $pdo->prepare(
        'SELECT a.id, a.appointment_date,
            a.appointment_time, a.status,
            s.service_name, s.price,
            b.beautician_name,
            p.id AS payment_id,
            p.payment_status
        FROM appointments a
        JOIN customers c 
        ON a.customer_id = c.id
        JOIN services s 
        ON a.service_id = s.id
        JOIN beauticians b 
        ON a.beautician_id = b.id
        LEFT JOIN payments p 
        ON p.appointment_id = a.id
        WHERE c.user_id = :user_id
        ORDER BY 
            a.appointment_date DESC,
            a.appointment_time DESC'
    );

    $query->execute([
        'user_id' => (int) $_SESSION['user_id']
    ]);

    $appointments = $query->fetchAll();

    $success = $_SESSION['success_message'] ?? '';

    unset($_SESSION['success_message']);

    $pageTitle = 'My bookings';

    require __DIR__ . '/header.php';

?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h2 mb-1">
                My bookings
            </h1>

            <p class="text-muted mb-0">
                Your upcoming and previous salon appointments.
            </p>

        </div>

        <a class="btn btn-primary" href="<?= BASE_URL ?>/customer/booking.php">
            Booking new appointment
        </a>

    </div>


    <?php if ($success): ?>

        <div class="alert alert-success">
            <?= e($success) ?>
        </div>

    <?php endif; ?>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-dark">

                    <tr>
                        <th>Visit</th>
                        <th>Service</th>
                        <th>Beautician</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Cancel</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (!$appointments): ?>

                        <tr>

                            <td colspan="6" class="text-center text-muted py-5">
                                No appointments yet.
                                Your first beauty appointment is only a few
                                clicks away.
                            </td>

                        </tr>

                    <?php endif; ?>


                    <?php foreach ($appointments as $a): ?>

                        <tr>

                            <td>

                                <?= e(
                                    date(
                                        'd M Y',
                                        strtotime($a['appointment_date'])
                                    )
                                ) ?>

                                <br>

                                <small class="text-muted">

                                    <?= e(
                                        date(
                                            'h:i A',
                                            strtotime($a['appointment_time'])
                                        )
                                    ) ?>

                                </small>

                            </td>

                            <td>

                                <?= e($a['service_name']) ?>

                                <br>

                                <small>
                                    ₹ <?= e(
                                        number_format(
                                            (float) $a['price'],
                                            2
                                        )
                                    ) ?>
                                </small>

                            </td>

                            <td>
                                <?= e($a['beautician_name']) ?>
                            </td>

                            <td>

                                <span class="badge bg-secondary">
                                    <?= e($a['status']) ?>
                                </span>

                            </td>

                            <td>

                                <?php if ($a['payment_id']): ?>

                                    <span class="badge bg-success">
                                        <?= e($a['payment_status']) ?>
                                    </span>

                                    <br>

                                    <a class="small" href="<?= BASE_URL ?>/payments/invoice.php?id=<?= (int) $a['payment_id'] ?>">
                                        Invoice
                                    </a>


                                <?php elseif ($a['status'] !== 'Cancelled'): ?>

                                    <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/customer/payment.php?appointment=<?= (int) $a['id'] ?>">
                                        Pay now
                                    </a>


                                <?php else: ?>

                                    <span class="text-muted">
                                        Cancelled
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if (
                                    $a['status'] !== 'Completed' &&
                                    $a['status'] !== 'Cancelled'
                                ): ?>

                                    <form method="post" action="<?= BASE_URL ?>/customer/cancel_appointment.php">

                                        <input type="hidden" name="appointment_id" value="<?= (int) $a['id'] ?>">

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Cancel
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="text-muted">
                                        Cancelled
                                    </span>

                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>


<?php require __DIR__ . "/footer.php"; ?>
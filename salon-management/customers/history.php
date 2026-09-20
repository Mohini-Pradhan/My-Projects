<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        $_SESSION['error_message'] = 'Invalid customer selected.';
        redirect('/customers/index.php');
    }

    $customerId = (int) $_GET['id'];

    $getCustomer = $pdo->prepare(
        'SELECT *
        FROM customers
        WHERE id = :id'
    );

    $getCustomer->execute(['id' => $customerId]);
    $customer = $getCustomer->fetch();

    if (!$customer) {
        $_SESSION['error_message'] = 'Customer not found.';
        redirect('/customers/index.php');
    }

    $getHistory = $pdo->prepare(
        'SELECT
            appointments.id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.status,
            services.service_name,
            services.price,
            beauticians.beautician_name,
            COALESCE(payment_totals.total_paid, 0) AS total_paid
        FROM appointments
        INNER JOIN services
            ON appointments.service_id = services.id
        INNER JOIN beauticians
            ON appointments.beautician_id = beauticians.id
        LEFT JOIN (
            SELECT
                appointment_id,
                SUM(amount) AS total_paid
            FROM payments
            WHERE payment_status IN ("Paid", "Partially Paid")
            GROUP BY appointment_id
        ) AS payment_totals
            ON appointments.id = payment_totals.appointment_id
        WHERE appointments.customer_id = :customer_id
        ORDER BY
            appointments.appointment_date DESC,
            appointments.appointment_time DESC'
    );

    $getHistory->execute([
        'customer_id' => $customerId
    ]);

    $history = $getHistory->fetchAll();

    $totalSpent = 0.0;

    foreach ($history as $item) {
        $totalSpent += (float) $item['total_paid'];
    }

    function historyStatusClass(string $status): string
    {
        return match ($status) {
            'Confirmed' => 'bg-primary',
            'Completed' => 'bg-success',
            'Cancelled' => 'bg-danger',
            default => 'bg-warning text-dark'
        };
    }

$pageTitle = 'Customer History';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">
                <?= e($customer['customer_name']) ?>’s History
            </h1>

            <p class="text-muted mb-0">
                <?= e($customer['phone']) ?>
                <?php if ($customer['email']): ?>
                    · <?= e($customer['email']) ?>
                <?php endif; ?>
            </p>
        </div>

        <a href="<?= BASE_URL ?>/customers/index.php" class="btn text-white" style="background-color: #8f1d4c;">
            Back to Customers
        </a>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Appointments</p>
                    <h2 class="display-6 mb-0" style="color: #8f1d4c;">
                        <?= count($history) ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Paid</p>
                    <h2 class="display-6 mb-0" style="color: #4a0f2a;">
                        ₹<?= e(number_format($totalSpent, 2)) ?>
                    </h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h2 class="h4 mb-3">Appointment History</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Date / Time</th>
                            <th>Service</th>
                            <th>Beautician</th>
                            <th>Service Price</th>
                            <th>Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    This customer has no appointments yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($history as $appointment): ?>
                                <tr>
                                    <td><?= (int) $appointment['id'] ?></td>

                                    <td>
                                        <?= e(date(
                                            'd M Y',
                                            strtotime(
                                                $appointment['appointment_date']
                                            )
                                        )) ?>
                                        <br>

                                        <small class="text-muted">
                                            <?= e(date(
                                                'h:i A',
                                                strtotime(
                                                    $appointment['appointment_time']
                                                )
                                            )) ?>
                                        </small>
                                    </td>

                                    <td><?= e($appointment['service_name']) ?></td>
                                    <td><?= e($appointment['beautician_name']) ?></td>

                                    <td>
                                        ₹<?= e(number_format(
                                            (float) $appointment['price'],
                                            2
                                        )) ?>
                                    </td>

                                    <td style="color: #4a0f2a;">
                                        ₹<?= e(number_format(
                                            (float) $appointment['total_paid'],
                                            2
                                        )) ?>
                                    </td>

                                    <td>
                                        <span class="badge" style="background-color: #4a0f2a; color: white;">
                                            <?= e($appointment['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

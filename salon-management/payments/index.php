<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    $appointments = $pdo->query(
        'SELECT
            appointments.id,
            appointments.appointment_date,
            customers.customer_name,
            services.service_name,
            services.price
        FROM appointments
        INNER JOIN customers
            ON appointments.customer_id = customers.id
        INNER JOIN services
            ON appointments.service_id = services.id
        WHERE appointments.status = "Completed"
        ORDER BY appointments.appointment_date DESC'
    )->fetchAll();

    $appointmentIds = array_column($appointments, 'id');
    $appointmentPrices = [];

    foreach ($appointments as $appointment) {
        $appointmentPrices[(int) $appointment['id']] =
            (float) $appointment['price'];
    }

    $errors = [];
    $appointmentId = '';
    $amount = '';
    $paymentMethod = 'Cash';
    $paymentStatus = 'Paid';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $appointmentId = $_POST['appointment_id'] ?? '';
        $amount = trim($_POST['amount'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? 'Cash');
        $paymentStatus = trim($_POST['payment_status'] ?? 'Paid');

        $allowedMethods = ['Cash', 'Card', 'UPI'];
        $allowedStatuses = ['Pending', 'Paid', 'Partially Paid', 'Refunded'];

        if (
            !ctype_digit($appointmentId) ||
            !in_array((int) $appointmentId, $appointmentIds, true)
        ) {
            $errors[] = 'Please select a completed appointment.';
        }

        if ($amount === '' || !is_numeric($amount) || (float) $amount <= 0) {
            $errors[] = 'Payment amount must be greater than zero.';
        }

        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $errors[] = 'Please select a valid payment method.';
        }

        if (!in_array($paymentStatus, $allowedStatuses, true)) {
            $errors[] = 'Please select a valid payment status.';
        }

        if (empty($errors)) {
            $appointmentId = (int) $appointmentId;
            $paymentAmount = (float) $amount;
            $servicePrice = $appointmentPrices[$appointmentId];

            $previousPayment = $pdo->prepare(
                'SELECT COALESCE(SUM(amount), 0)
                FROM payments
                WHERE appointment_id = :appointment_id
                AND payment_status IN ("Paid", "Partially Paid")'
            );

            $previousPayment->execute([
                'appointment_id' => $appointmentId
            ]);

            $alreadyPaid = (float) $previousPayment->fetchColumn();

            if (
                in_array($paymentStatus, ['Paid', 'Partially Paid'], true) &&
                ($alreadyPaid + $paymentAmount) > $servicePrice
            ) {
                $errors[] =
                    'Payment amount is more than the remaining service balance.';
            }

            if (empty($errors)) {
                do {
                    $invoiceNumber =
                        'INV-' . date('Ymd') . '-' . random_int(1000, 9999);

                    $invoiceCheck = $pdo->prepare(
                        'SELECT id
                        FROM payments
                        WHERE invoice_number = :invoice_number'
                    );

                    $invoiceCheck->execute([
                        'invoice_number' => $invoiceNumber
                    ]);
                } while ($invoiceCheck->fetch());

                $addPayment = $pdo->prepare(
                    'INSERT INTO payments
                    (
                        appointment_id,
                        amount,
                        payment_method,
                        payment_status,
                        invoice_number,
                        paid_at
                    )
                    VALUES
                    (
                        :appointment_id,
                        :amount,
                        :payment_method,
                        :payment_status,
                        :invoice_number,
                        :paid_at
                    )'
                );

                $paidAt = in_array(
                    $paymentStatus,
                    ['Paid', 'Partially Paid'],
                    true
                ) ? date('Y-m-d H:i:s') : null;

                $addPayment->execute([
                    'appointment_id' => $appointmentId,
                    'amount' => $paymentAmount,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'invoice_number' => $invoiceNumber,
                    'paid_at' => $paidAt
                ]);

                $_SESSION['success_message'] =
                    'Payment saved. Invoice number: ' . $invoiceNumber;

                redirect('/payments/index.php');
            }
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['error_message']);

    $getPayments = $pdo->query(
        'SELECT
            payments.id,
            payments.amount,
            payments.payment_method,
            payments.payment_status,
            payments.invoice_number,
            payments.paid_at,
            customers.customer_name,
            services.service_name
        FROM payments
        INNER JOIN appointments
            ON payments.appointment_id = appointments.id
        INNER JOIN customers
            ON appointments.customer_id = customers.id
        INNER JOIN services
            ON appointments.service_id = services.id
        ORDER BY payments.id DESC'
    );

    $payments = $getPayments->fetchAll();

    function paymentStatusClass(string $status): string
    {
        return match ($status) {
            'Paid' => 'bg-success',
            'Partially Paid' => 'bg-warning text-dark',
            'Refunded' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
?>

<?php
    $pageTitle = 'Payments';
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h4 mb-3">Record Payment</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($appointments)): ?>
                        <div class="alert alert-warning mb-0">
                            No completed appointments are available for payment.
                        </div>
                    <?php else: ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label for="appointment_id" class="form-label">
                                    Completed Appointment *
                                </label>

                                <select
                                    class="form-select"
                                    id="appointment_id"
                                    name="appointment_id"
                                    required
                                >
                                    <option value="">Select appointment</option>

                                    <?php foreach ($appointments as $appointment): ?>
                                        <option
                                            value="<?= (int) $appointment['id'] ?>"
                                            <?= (string) $appointmentId ===
                                                (string) $appointment['id']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            #<?= (int) $appointment['id'] ?>
                                            - <?= e($appointment['customer_name']) ?>
                                            - <?= e($appointment['service_name']) ?>
                                            (₹<?= e(number_format(
                                                (float) $appointment['price'],
                                                2
                                            )) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">
                                    Amount *
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="amount"
                                    name="amount"
                                    min="0.01"
                                    step="0.01"
                                    value="<?= e($amount) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">
                                    Payment Method *
                                </label>

                                <select
                                    class="form-select"
                                    id="payment_method"
                                    name="payment_method"
                                    required
                                >
                                    <?php foreach (
                                        ['Cash', 'Card', 'UPI']
                                        as $method
                                    ): ?>
                                        <option
                                            value="<?= e($method) ?>"
                                            <?= $paymentMethod === $method
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($method) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="payment_status" class="form-label">
                                    Payment Status *
                                </label>

                                <select
                                    class="form-select"
                                    id="payment_status"
                                    name="payment_status"
                                    required
                                >
                                    <?php foreach (
                                        [
                                            'Pending',
                                            'Paid',
                                            'Partially Paid',
                                            'Refunded'
                                        ] as $status
                                    ): ?>
                                        <option
                                            value="<?= e($status) ?>"
                                            <?= $paymentStatus === $status
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn text-white" style="background-color: #8f1d4c; width: 100%;">
                                Save Payment
                            </button>

                        </form>

                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 mb-0">Payment Records</h2>
                        <span class="badge text-white" style="background-color: #4a0f2a;">
                            <?= count($payments) ?> Total
                        </span>
                    </div>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <?= e($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger">
                            <?= e($errorMessage) ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Paid On</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No payment records found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= e($payment['invoice_number']) ?></td>
                                            <td><?= e($payment['customer_name']) ?></td>
                                            <td><?= e($payment['service_name']) ?></td>
                                            <td>
                                                ₹<?= e(number_format(
                                                    (float) $payment['amount'],
                                                    2
                                                )) ?>
                                            </td>
                                            <td><?= e($payment['payment_method']) ?></td>
                                            <td>
                                                <span class="badge <?= e(
                                                    paymentStatusClass(
                                                        $payment['payment_status']
                                                    )
                                                ) ?>">
                                                    <?= e($payment['payment_status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $payment['paid_at']
                                                    ? e(date(
                                                        'd M Y, h:i A',
                                                        strtotime(
                                                            $payment['paid_at']
                                                        )
                                                    ))
                                                    : '-' ?>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/payments/invoice.php?id=<?= (int) $payment['id'] ?>" class="btn text-white" style="background-color: #8f1d4c;">
                                                    View
                                                </a>
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

    </div>
</div>

</body>
</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

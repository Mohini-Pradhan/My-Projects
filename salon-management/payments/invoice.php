<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireLogin();

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        $_SESSION['error_message'] = 'Invalid invoice selected.';
        redirect('/payments/index.php');
    }

    $paymentId = (int) $_GET['id'];

    $getInvoice = $pdo->prepare(
        'SELECT
            payments.id AS payment_id,
            payments.amount,
            payments.payment_method,
            payments.payment_status,
            payments.invoice_number,
            payments.paid_at,
            appointments.id AS appointment_id,
            appointments.customer_id,
            appointments.appointment_date,
            appointments.appointment_time,
            customers.customer_name,
            customers.phone AS customer_phone,
            customers.email AS customer_email,
            customers.address AS customer_address,
            services.service_name,
            services.price AS service_price,
            services.duration,
            beauticians.beautician_name
        FROM payments
        INNER JOIN appointments
            ON payments.appointment_id = appointments.id
        INNER JOIN customers
            ON appointments.customer_id = customers.id
        INNER JOIN services
            ON appointments.service_id = services.id
        INNER JOIN beauticians
            ON appointments.beautician_id = beauticians.id
        WHERE payments.id = :id'
    );

    $getInvoice->execute(['id' => $paymentId]);
    $invoice = $getInvoice->fetch();

    if (!$invoice) {
        $_SESSION['error_message'] = 'Invoice not found.';
        redirect('/payments/index.php');
    }

    if (isCustomer()) {
        $owner = $pdo->prepare(
            'SELECT id FROM customers WHERE id = :customer_id AND user_id = :user_id'
        );
        $owner->execute([
            'customer_id' => (int) $invoice['customer_id'],
            'user_id' => (int) $_SESSION['user_id']
        ]);
        if (!$owner->fetch()) {
            $_SESSION['error_message'] = 'You cannot view this invoice.';
            redirect('/customer/appointments.php');
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($invoice['invoice_number']) ?> | Salon Management</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            .card {
                box-shadow: none !important;
                border: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between mb-4 no-print">
        <a href="<?= BASE_URL ?>/payments/index.php" class="btn text-white" style="background-color: #4a0f2a;">
            Back to Payments
        </a>

        <button class="btn text-white" style="background-color: #4a0f2a;" onclick="window.print()">
            Print Invoice
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">

            <div class="row mb-5">
                <div class="col-7">
                    <h1 class="h2 fw-bold mb-1">Salon Management</h1>
                    <p class="text-muted mb-0">
                        Salon & Beauty Studio Management System
                    </p>
                </div>

                <div class="col-5 text-end">
                    <h2 class="h4 mb-2">INVOICE</h2>
                    <p class="mb-1">
                        <strong>Invoice:</strong>
                        <?= e($invoice['invoice_number']) ?>
                    </p>
                    <p class="mb-0">
                        <strong>Date:</strong>
                        <?= e(date(
                            'd M Y',
                            strtotime($invoice['paid_at'] ?? 'now')
                        )) ?>
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h3 class="h6 text-uppercase text-muted">Bill To</h3>
                    <p class="mb-1 fw-semibold">
                        <?= e($invoice['customer_name']) ?>
                    </p>
                    <p class="mb-1"><?= e($invoice['customer_phone']) ?></p>

                    <?php if ($invoice['customer_email']): ?>
                        <p class="mb-1"><?= e($invoice['customer_email']) ?></p>
                    <?php endif; ?>

                    <?php if ($invoice['customer_address']): ?>
                        <p class="mb-0"><?= e($invoice['customer_address']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mt-4 mt-md-0">
                    <h3 class="h6 text-uppercase text-muted">
                        Appointment Details
                    </h3>
                    <p class="mb-1">
                        <strong>Appointment #:</strong>
                        <?= (int) $invoice['appointment_id'] ?>
                    </p>
                    <p class="mb-1">
                        <strong>Date:</strong>
                        <?= e(date(
                            'd M Y',
                            strtotime($invoice['appointment_date'])
                        )) ?>
                    </p>
                    <p class="mb-1">
                        <strong>Time:</strong>
                        <?= e(date(
                            'h:i A',
                            strtotime($invoice['appointment_time'])
                        )) ?>
                    </p>
                    <p class="mb-0">
                        <strong>Beautician:</strong>
                        <?= e($invoice['beautician_name']) ?>
                    </p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>Service</th>
                            <th>Duration</th>
                            <th class="text-end">Service Price</th>
                            <th class="text-end">Payment Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= e($invoice['service_name']) ?></td>
                            <td><?= (int) $invoice['duration'] ?> minutes</td>
                            <td class="text-end">
                                ₹<?= e(number_format(
                                    (float) $invoice['service_price'],
                                    2
                                )) ?>
                            </td>
                            <td class="text-end">
                                ₹<?= e(number_format(
                                    (float) $invoice['amount'],
                                    2
                                )) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Method</span>
                            <strong><?= e($invoice['payment_method']) ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Status</span>
                            <strong><?= e($invoice['payment_status']) ?></strong>
                        </div>

                        <div class="d-flex justify-content-between fs-5 border-top pt-2">
                            <strong>Amount Paid</strong>
                            <strong>
                                ₹ <?= e(number_format(
                                    (float) $invoice['amount'],
                                    2
                                )) ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-center text-muted small mt-5 mb-0">
                Thank you for choosing our salon.
            </p>

        </div>
    </div>

</div>

</body>
</html>

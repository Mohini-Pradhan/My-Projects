<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');

    $startObject = DateTime::createFromFormat('Y-m-d', $startDate);
    $endObject = DateTime::createFromFormat('Y-m-d', $endDate);

    if (
        !$startObject ||
        !$endObject ||
        $startObject->format('Y-m-d') !== $startDate ||
        $endObject->format('Y-m-d') !== $endDate ||
        $startDate > $endDate
    ) {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');
    }

    $appointmentSummary = $pdo->prepare(
        'SELECT
            COUNT(*) AS total_appointments,
            SUM(status = "Pending") AS pending_count,
            SUM(status = "Confirmed") AS confirmed_count,
            SUM(status = "Completed") AS completed_count,
            SUM(status = "Cancelled") AS cancelled_count
        FROM appointments
        WHERE appointment_date BETWEEN :start_date AND :end_date'
    );

    $appointmentSummary->execute([
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);

    $appointmentSummary = $appointmentSummary->fetch();

    $totalAppointments = (int) $appointmentSummary['total_appointments'];

        $pendingCount = (int) $appointmentSummary['pending_count'];
        $confirmedCount = (int) $appointmentSummary['confirmed_count'];
        $completedCount = (int) $appointmentSummary['completed_count'];
        $cancelledCount = (int) $appointmentSummary['cancelled_count'];

        $pendingPercent = $totalAppointments > 0
            ? round(($pendingCount / $totalAppointments) * 100)
            : 0;

        $confirmedPercent = $totalAppointments > 0
            ? round(($confirmedCount / $totalAppointments) * 100)
            : 0;

        $completedPercent = $totalAppointments > 0
            ? round(($completedCount / $totalAppointments) * 100)
            : 0;

        $cancelledPercent = $totalAppointments > 0
            ? round(($cancelledCount / $totalAppointments) * 100)
            : 0;

    $revenueSummary = $pdo->prepare(
        'SELECT COALESCE(SUM(amount), 0)
        FROM payments
        WHERE payment_status IN ("Paid", "Partially Paid")
        AND DATE(paid_at) BETWEEN :start_date AND :end_date'
    );

    $revenueSummary->execute([
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);

    $totalRevenue = (float) $revenueSummary->fetchColumn();

    $newCustomerSummary = $pdo->prepare(
        'SELECT COUNT(*)
        FROM customers
        WHERE DATE(created_at) BETWEEN :start_date AND :end_date'
    );

    $newCustomerSummary->execute([
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);

    $newCustomers = (int) $newCustomerSummary->fetchColumn();

    $dailyRevenue = $pdo->prepare(
        'SELECT
            DATE(paid_at) AS payment_date,
            SUM(amount) AS revenue
        FROM payments
        WHERE payment_status IN ("Paid", "Partially Paid")
        AND DATE(paid_at) BETWEEN :start_date AND :end_date
        GROUP BY DATE(paid_at)
        ORDER BY payment_date DESC'
    );

    $dailyRevenue->execute([
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);

    $dailyRevenue = $dailyRevenue->fetchAll();

    $appointmentReport = $pdo->prepare(
        'SELECT
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.status,
            customers.customer_name,
            services.service_name,
            beauticians.beautician_name
        FROM appointments
        INNER JOIN customers
            ON appointments.customer_id = customers.id
        INNER JOIN services
            ON appointments.service_id = services.id
        INNER JOIN beauticians
            ON appointments.beautician_id = beauticians.id
        WHERE appointments.appointment_date
            BETWEEN :start_date AND :end_date
        ORDER BY
            appointments.appointment_date DESC,
            appointments.appointment_time DESC'
    );

    $appointmentReport->execute([
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);

    $appointmentReport = $appointmentReport->fetchAll();

    function reportStatusClass(string $status): string
    {
        return match ($status) {
            'Confirmed' => 'bg-primary',
            'Completed' => 'bg-success',
            'Cancelled' => 'bg-danger',
            default => 'bg-warning text-dark'
        };
    }

    $pageTitle = 'Reports';
    require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
        <div>
            <h1 class="h2 mb-1">Reports</h1>
            <p class="text-muted mb-0">
                Review appointments, new customers, and revenue.
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label for="start_date" class="form-label">
                        Start Date
                    </label>
                    <input
                        type="date"
                        class="form-control"
                        id="start_date"
                        name="start_date"
                        value="<?= e($startDate) ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label for="end_date" class="form-label">
                        End Date
                    </label>
                    <input
                        type="date"
                        class="form-control"
                        id="end_date"
                        name="end_date"
                        value="<?= e($endDate) ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                        Generate Report
                    </button>

                    <a
                        href="<?= BASE_URL ?>/reports/index.php"
                        class="btn text-white" style="background-color: #8f1d4c;"
                    >
                        Reset
                    </a>
                </div>

            </form>

        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Appointments</p>
                    <h2 class="display-6 mb-0" style="color: #8f1d4c;">
                        <?= (int) $appointmentSummary['total_appointments'] ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Completed</p>
                    <h2 class="display-6 mb-0" style="color: #8f1d4c;">
                        <?= (int) $appointmentSummary['completed_count'] ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">New Customers</p>
                    <h2 class="display-6 mb-0" style="color: #8f1d4c;">
                        <?= $newCustomers ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-white overflow-hidden"
                style="background: linear-gradient(135deg, #4a0f2a, #8f1d4c, #c12f68);"
            >
                <div class="card-body p-4 position-relative">

                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                        style="
                            width: 52px;
                            height: 52px;
                            font-size: 1.5rem;
                            background: rgba(255, 255, 255, 0.18);
                            border: 1px solid rgba(255, 255, 255, 0.25);
                        "
                    >
                        ₹
                    </div>

                    <p class="text-white-50 mb-2">
                        Total Revenue
                    </p>

                    <h2 class="display-6 fw-bold mb-0">
                        ₹<?= e(number_format($totalRevenue, 2)) ?>
                    </h2>

                    <small class="d-block mt-2 text-white-50">
                        Paid and partially paid payments
                    </small>

                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h2 class="h5 mb-4">Appointment Status</h2>

                    <div class="text-center mb-4">
                        <div
                            class="rounded-circle d-inline-flex flex-column align-items-center justify-content-center"
                            style="
                                width: 150px;
                                height: 150px;
                                color: #4a0f2a;
                                background:
                                    conic-gradient(
                                        #198754 0% <?= $completedPercent ?>%,
                                        #0d6efd <?= $completedPercent ?>%
                                            <?= $completedPercent + $confirmedPercent ?>%,
                                        #ffc107 <?= $completedPercent + $confirmedPercent ?>%
                                            <?= $completedPercent + $confirmedPercent + $pendingPercent ?>%,
                                        #dc3545 <?= $completedPercent + $confirmedPercent + $pendingPercent ?>%
                                            100%
                                    );
                            "
                        >
                            <div
                                class="rounded-circle d-flex flex-column align-items-center justify-content-center"
                                style="
                                    width: 110px;
                                    height: 110px;
                                    background: #ffffff;
                                "
                            >
                                <strong class="fs-3">
                                    <?= $totalAppointments ?>
                                </strong>

                                <small class="text-muted">
                                    Total
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>
                            <span class="badge bg-warning text-dark me-1">●</span>
                            Pending
                        </span>

                        <strong><?= $pendingCount ?></strong>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>
                            <span class="badge bg-primary me-1">●</span>
                            Confirmed
                        </span>

                        <strong><?= $confirmedCount ?></strong>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>
                            <span class="badge bg-success me-1">●</span>
                            Completed
                        </span>

                        <strong><?= $completedCount ?></strong>
                    </div>

                    <div class="d-flex justify-content-between py-2">
                        <span>
                            <span class="badge bg-danger me-1">●</span>
                            Cancelled
                        </span>

                        <strong><?= $cancelledCount ?></strong>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h2 class="h5 mb-3">Daily Revenue</h2>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($dailyRevenue)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            No payment revenue in this period.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($dailyRevenue as $revenue): ?>
                                        <tr>
                                            <td>
                                                <?= e(date(
                                                    'd M Y',
                                                    strtotime(
                                                        $revenue['payment_date']
                                                    )
                                                )) ?>
                                            </td>

                                            <td class="text-end fw-semibold" style="color: #8f1d4c;">
                                                ₹ <?= e(number_format(
                                                    (float) $revenue['revenue'],
                                                    2
                                                )) ?>
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

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">

            <h2 class="h5 mb-3">Appointments in Selected Period</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date / Time</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Beautician</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($appointmentReport)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No appointments found for this period.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointmentReport as $appointment): ?>
                                <tr>
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

                                    <td><?= e($appointment['customer_name']) ?></td>
                                    <td><?= e($appointment['service_name']) ?></td>
                                    <td><?= e($appointment['beautician_name']) ?></td>

                                    <td>
                                        <span class="badge <?= e(
                                            reportStatusClass(
                                                $appointment['status']
                                            )
                                        ) ?>">
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

<?php
    declare(strict_types=1);

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/includes/auth.php';

    //redirect to customer index if not logged in or logged in as customer
    if (!isLoggedIn()) {
    require_once __DIR__ . '/portal.php';
    exit;
    }

    if (isCustomer()) {
        require_once __DIR__ . '/customer/index.php';
        exit;
    }

    requireAdmin();

    $customerCount = (int) $pdo->query(
        'SELECT COUNT(*) FROM customers'
    )->fetchColumn();

    $serviceCount = (int) $pdo->query(
        'SELECT COUNT(*) FROM services'
    )->fetchColumn();

    $beauticianCount = (int) $pdo->query(
        'SELECT COUNT(*) FROM beauticians'
    )->fetchColumn();

    $appointmentCount = (int) $pdo->query(
        'SELECT COUNT(*) FROM appointments'
    )->fetchColumn();

    $totalRevenue = (float) $pdo->query(
        'SELECT COALESCE(SUM(amount), 0)
        FROM payments
        WHERE payment_status IN ("Paid", "Partially Paid")'
    )->fetchColumn();

    $todayAppointments = $pdo->query(
        'SELECT appointments.id,
                appointments.appointment_time,
                appointments.status,
                customers.customer_name,
                services.service_name,
                beauticians.beautician_name
        FROM appointments
        INNER JOIN customers ON appointments.customer_id = customers.id
        INNER JOIN services ON appointments.service_id = services.id
        INNER JOIN beauticians ON appointments.beautician_id = beauticians.id
        WHERE appointments.appointment_date = CURDATE()
        ORDER BY appointments.appointment_time ASC'
    )->fetchAll();

    function dashboardStatusClass(string $status): string
    {
        return match ($status) {
            'Confirmed' => 'bg-primary',
            'Completed' => 'bg-success',
            'Cancelled' => 'bg-danger',
            default => 'bg-warning text-dark'
        };
    }

    $pageTitle = 'Admin Dashboard';
    require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Welcome, <?= e($_SESSION['user_name']) ?></h1>
            <p class="text-muted mb-0">Here is your salon business overview.</p>
        </div>
        <a href="<?= BASE_URL ?>/appointments/index.php" class="btn text-white" style="background-color:#8f1d4c;">
            Manage Appointments
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <a href="<?= BASE_URL ?>/customers/index.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Customers</p>
                        <h2 class="display-6 mb-0"><?= $customerCount ?></h2>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="<?= BASE_URL ?>/services/index.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Services</p>
                        <h2 class="display-6 mb-0"><?= $serviceCount ?></h2>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="<?= BASE_URL ?>/beauticians/index.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Beauticians</p>
                        <h2 class="display-6 mb-0"><?= $beauticianCount ?></h2>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="<?= BASE_URL ?>/appointments/index.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Appointments</p>
                        <h2 class="display-6 mb-0"><?= $appointmentCount ?></h2>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12">
            <a href="<?= BASE_URL ?>/payments/index.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-2">Total Revenue</p>
                        <h2 class="display-6 mb-0" style="color:#8f1d4c;">
                            ₹<?= e(number_format($totalRevenue, 2)) ?>
                        </h2>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h4 mb-3">Today's Appointments</h2>

            <?php 
                if (!$todayAppointments): 
            ?>
                <p class="text-muted mb-0">No appointments are scheduled for today.</p>
                <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Time</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Beautician</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todayAppointments as $appointment): ?>
                                <tr>
                                    <td><?= e(date('h:i A', strtotime($appointment['appointment_time']))) ?></td>
                                    <td><?= e($appointment['customer_name']) ?></td>
                                    <td><?= e($appointment['service_name']) ?></td>
                                    <td><?= e($appointment['beautician_name']) ?></td>
                                    <td>
                                        <span class="badge <?= dashboardStatusClass($appointment['status']) ?>">
                                            <?= e($appointment['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
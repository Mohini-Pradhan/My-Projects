<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    $customers = $pdo->query(
        'SELECT id, customer_name, phone
        FROM customers
        ORDER BY customer_name ASC'
    )->fetchAll();

    $services = $pdo->query(
        'SELECT id, service_name, price, duration
        FROM services
        ORDER BY service_name ASC'
    )->fetchAll();

    $beauticians = $pdo->query(
        'SELECT id, beautician_name, specialization
        FROM beauticians
        ORDER BY beautician_name ASC'
    )->fetchAll();

    $customerIds = array_column($customers, 'id');
    $serviceIds = array_column($services, 'id');
    $beauticianIds = array_column($beauticians, 'id');

    $serviceDurations = [];

    foreach ($services as $service) {
        $serviceDurations[(int) $service['id']] = (int) $service['duration'];
    }

    $errors = [];
    $customerId = '';
    $serviceId = '';
    $beauticianId = '';
    $appointmentDate = '';
    $appointmentTime = '';
    $status = 'Pending';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customerId = $_POST['customer_id'] ?? '';
        $serviceId = $_POST['service_id'] ?? '';
        $beauticianId = $_POST['beautician_id'] ?? '';
        $appointmentDate = trim($_POST['appointment_date'] ?? '');
        $appointmentTime = trim($_POST['appointment_time'] ?? '');
        $status = trim($_POST['status'] ?? 'Pending');

        $allowedStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

        if (
            !ctype_digit($customerId) ||
            !in_array((int) $customerId, $customerIds, true)
        ) {
            $errors[] = 'Please select a valid customer.';
        }

        if (
            !ctype_digit($serviceId) ||
            !in_array((int) $serviceId, $serviceIds, true)
        ) {
            $errors[] = 'Please select a valid service.';
        }

        if (
            !ctype_digit($beauticianId) ||
            !in_array((int) $beauticianId, $beauticianIds, true)
        ) {
            $errors[] = 'Please select a valid beautician.';
        }

        if (
            $appointmentDate === '' ||
            !DateTime::createFromFormat('Y-m-d', $appointmentDate)
        ) {
            $errors[] = 'Please choose a valid appointment date.';
        } elseif ($appointmentDate < date('Y-m-d')) {
            $errors[] = 'Appointment date cannot be in the past.';
        }

        $timeObject = DateTime::createFromFormat('H:i', $appointmentTime);

        if (
            $appointmentTime === '' ||
            !$timeObject ||
            $timeObject->format('H:i') !== $appointmentTime
        ) {
            $errors[] = 'Please choose a valid appointment time.';
        }

        if (!in_array($status, $allowedStatuses, true)) {
            $errors[] = 'Please select a valid appointment status.';
        }

        if (empty($errors)) {
            $customerId = (int) $customerId;
            $serviceId = (int) $serviceId;
            $beauticianId = (int) $beauticianId;

            $serviceDuration = $serviceDurations[$serviceId];

            $bookingStart = $appointmentDate . ' ' . $appointmentTime;

            $bookingEnd = (new DateTime($bookingStart))
                ->modify('+' . $serviceDuration . ' minutes')
                ->format('Y-m-d H:i:s');

            $conflictCheck = $pdo->prepare(
                'SELECT COUNT(*)
                FROM appointments
                INNER JOIN services
                    ON appointments.service_id = services.id
                WHERE appointments.beautician_id = :beautician_id
                AND appointments.appointment_date = :appointment_date
                AND appointments.status IN ("Pending", "Confirmed")
                AND TIMESTAMP(appointments.appointment_date, appointments.appointment_time)
                    < :booking_end
                AND DATE_ADD(
                        TIMESTAMP(
                            appointments.appointment_date,
                            appointments.appointment_time
                        ),
                        INTERVAL services.duration MINUTE
                ) > :booking_start'
            );

            $conflictCheck->execute([
                'beautician_id' => $beauticianId,
                'appointment_date' => $appointmentDate,
                'booking_start' => $bookingStart,
                'booking_end' => $bookingEnd
            ]);

            if ((int) $conflictCheck->fetchColumn() > 0) {
                $errors[] =
                    'This beautician already has an overlapping appointment.';
            } else {
                $addAppointment = $pdo->prepare(
                    'INSERT INTO appointments
                    (
                        customer_id,
                        service_id,
                        beautician_id,
                        appointment_date,
                        appointment_time,
                        status
                    )
                    VALUES
                    (
                        :customer_id,
                        :service_id,
                        :beautician_id,
                        :appointment_date,
                        :appointment_time,
                        :status
                    )'
                );

                $addAppointment->execute([
                    'customer_id' => $customerId,
                    'service_id' => $serviceId,
                    'beautician_id' => $beauticianId,
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => $appointmentTime,
                    'status' => $status
                ]);

                $_SESSION['success_message'] =
                    'Appointment booked successfully.';

                redirect('/appointments/index.php');
            }
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['error_message']);

    $getAppointments = $pdo->query(
        'SELECT
            appointments.id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.status,
            customers.customer_name,
            services.service_name,
            services.price,
            beauticians.beautician_name
            FROM appointments
            INNER JOIN customers
            ON appointments.customer_id = customers.id
            INNER JOIN services
            ON appointments.service_id = services.id
            INNER JOIN beauticians
            ON appointments.beautician_id = beauticians.id
            ORDER BY
            appointments.appointment_date DESC,
            appointments.appointment_time DESC'
    );

    $appointments = $getAppointments->fetchAll();

    function appointmentStatusClass(string $status): string
    {
        return match ($status) {
            'Confirmed' => 'bg-primary',
            'Completed' => 'bg-success',
            'Cancelled' => 'bg-danger',
            default => 'bg-warning text-dark'
        };
    }
?>

<?php
    $pageTitle = 'Appointments';
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h4 mb-3">Book Appointment</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (
                        empty($customers) ||
                        empty($services) ||
                        empty($beauticians)
                    ): ?>
                        <div class="alert alert-warning mb-0">
                            Add at least one customer, service, and beautician
                            before booking an appointment.
                        </div>
                    <?php else: ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label for="customer_id" class="form-label">
                                    Customer *
                                </label>
                                <select
                                    class="form-select"
                                    id="customer_id"
                                    name="customer_id"
                                    required
                                >
                                    <option value="">Select customer</option>

                                    <?php foreach ($customers as $customer): ?>
                                        <option
                                            value="<?= (int) $customer['id'] ?>"
                                            <?= (string) $customerId ===
                                                (string) $customer['id']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($customer['customer_name']) ?>
                                            - <?= e($customer['phone']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="service_id" class="form-label">
                                    Service *
                                </label>
                                <select
                                    class="form-select"
                                    id="service_id"
                                    name="service_id"
                                    required
                                >
                                    <option value="">Select service</option>

                                    <?php foreach ($services as $service): ?>
                                        <option
                                            value="<?= (int) $service['id'] ?>"
                                            <?= (string) $serviceId ===
                                                (string) $service['id']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($service['service_name']) ?>
                                            - ₹ <?= e(number_format(
                                                (float) $service['price'],
                                                2
                                            )) ?>
                                            (<?= (int) $service['duration'] ?> min)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="beautician_id" class="form-label">
                                    Beautician *
                                </label>
                                <select
                                    class="form-select"
                                    id="beautician_id"
                                    name="beautician_id"
                                    required
                                >
                                    <option value="">Select beautician</option>

                                    <?php foreach ($beauticians as $beautician): ?>
                                        <option
                                            value="<?= (int) $beautician['id'] ?>"
                                            <?= (string) $beauticianId ===
                                                (string) $beautician['id']
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($beautician['beautician_name']) ?>
                                            <?php if ($beautician['specialization']): ?>
                                                - <?= e(
                                                    $beautician['specialization']
                                                ) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">
                                    Appointment Date *
                                </label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="appointment_date"
                                    name="appointment_date"
                                    min="<?= date('Y-m-d') ?>"
                                    value="<?= e($appointmentDate) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-3">
                                <label for="appointment_time" class="form-label">
                                    Appointment Time *
                                </label>
                                <input
                                    type="time"
                                    class="form-control"
                                    id="appointment_time"
                                    name="appointment_time"
                                    value="<?= e($appointmentTime) ?>"
                                    required
                                >
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label">
                                    Status *
                                </label>
                                <select
                                    class="form-select"
                                    id="status"
                                    name="status"
                                    required
                                >
                                    <?php foreach (
                                        [
                                            'Pending',
                                            'Confirmed',
                                            'Completed',
                                            'Cancelled'
                                        ] as $option
                                    ): ?>
                                        <option
                                            value="<?= e($option) ?>"
                                            <?= $status === $option
                                                ? 'selected'
                                                : '' ?>
                                        >
                                            <?= e($option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn text-white" style="background-color: #8f1d4c; width: 100%;">
                                Book Appointment
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
                        <h2 class="h4 mb-0">Appointment List</h2>
                        <span class="badge text-white" style="background-color: #4a0f2a;">
                            <?= count($appointments) ?> Total
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
                                    <th>#</th>
                                    <th>Date / Time</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Beautician</th>
                                    <th>Status</th>
                                    <th>Update</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($appointments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No appointments found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($appointments as $appointment): ?>
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

                                            <td><?= e($appointment['customer_name']) ?></td>

                                            <td>
                                                <?= e($appointment['service_name']) ?>
                                                <br>
                                                <small class="text-muted">
                                                    ₹ <?= e(number_format(
                                                        (float) $appointment['price'],
                                                        2
                                                    )) ?>
                                                </small>
                                            </td>

                                            <td><?= e($appointment['beautician_name']) ?></td>

                                            <td>
                                                <span class="badge <?= e(
                                                    appointmentStatusClass(
                                                        $appointment['status']
                                                    )
                                                ) ?>">
                                                    <?= e($appointment['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="<?= BASE_URL ?>/appointments/update_status.php" class="d-flex gap-1">
                                                    <input type="hidden" name="id" value="<?= (int) $appointment['id'] ?>">

                                                    <select name="status" class="form-select form-select-sm">
                                                        <?php foreach (
                                                            ['Pending', 'Confirmed', 'Completed', 'Cancelled']
                                                            as $statusOption
                                                        ): ?>
                                                            <option
                                                                value="<?= e($statusOption) ?>"
                                                                <?= $appointment['status'] === $statusOption
                                                                    ? 'selected'
                                                                    : '' ?>
                                                            >
                                                                <?= e($statusOption) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>

                                                    <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                                                        Save
                                                    </button>
                                                </form>
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

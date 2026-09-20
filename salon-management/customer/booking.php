<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    if (!isLoggedIn()) {
        redirect('/customer/register.php');
    }

    requireCustomer();

    $customerQuery = $pdo->prepare(
        'SELECT id
        FROM customers
        WHERE user_id = :user_id
        LIMIT 1'
    );

    $customerQuery->execute([
        'user_id' => (int) $_SESSION['user_id']
    ]);

    $customer = $customerQuery->fetch();

    if (!$customer) {
        $_SESSION['error_message'] =
            'Your customer profile could not be found.';

        redirect('/customer/index.php');
    }

    $services = $pdo->query(
        'SELECT id, service_name, price, duration
        FROM services
        ORDER BY service_name'
    )->fetchAll();

    $beauticians = $pdo->query(
        'SELECT id, beautician_name, specialization
        FROM beauticians
        ORDER BY beautician_name'
    )->fetchAll();

    $salonSlots = [
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00'
    ];

    $errors = [];

    $serviceId = (int) (
        $_POST['service_id']
        ?? $_GET['service']
        ?? 0
    );

    $beauticianId = (int) (
        $_POST['beautician_id']
        ?? $_GET['beautician_id']
        ?? 0
    );

    $date = $_POST['appointment_date']
        ?? $_GET['appointment_date']
        ?? '';

    $time = $_POST['appointment_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    $bookedTimes = [];

    if ($beauticianId > 0 && $date !== '') {
        $getBookedTimes = $pdo->prepare(
            'SELECT appointment_time
            FROM appointments
            WHERE beautician_id = :beautician_id
            AND appointment_date = :appointment_date
            AND status != "Cancelled"'
        );

        $getBookedTimes->execute([
            'beautician_id' => $beauticianId,
            'appointment_date' => $date
        ]);

        $bookedTimes = $getBookedTimes->fetchAll(PDO::FETCH_COLUMN);
    }

    $isBeauticianOnLeave = false;

    if ($beauticianId > 0 && $date !== '') {
        $leaveCheck = $pdo->prepare(
            'SELECT id
            FROM beautician_leaves
            WHERE beautician_id = :beautician_id
            AND leave_date = :leave_date
            LIMIT 1'
        );

        $leaveCheck->execute([
            'beautician_id' => $beauticianId,
            'leave_date' => $date
        ]);

        $isBeauticianOnLeave = (bool) $leaveCheck->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $validDate = DateTime::createFromFormat(
            'Y-m-d',
            $date
        );

        if (
            !$serviceId ||
            !$beauticianId ||
            !$validDate ||
            $validDate->format('Y-m-d') !== $date ||
            $date < date('Y-m-d') ||
            !in_array($time, $salonSlots, true)
        ) {
            $errors[] =
                'Please select a service, beautician, valid future date, and available time.';
        }

        if ($isBeauticianOnLeave) {
            $errors[] =
                'This beautician is on leave on the selected date. Please choose another beautician or date.';
        }

        if (!$errors) {
            $conflict = $pdo->prepare(
                'SELECT id
                FROM appointments
                WHERE beautician_id = :beautician_id
                AND appointment_date = :date
                AND appointment_time = :time
                AND status != "Cancelled"'
            );

            $conflict->execute([
                'beautician_id' => $beauticianId,
                'date' => $date,
                'time' => $time . ':00'
            ]);

            if ($conflict->fetch()) {
                $errors[] =
                    'This beautician is already booked at that time. Please choose another slot.';
            }
        }

        if (!$errors) {
            $insert = $pdo->prepare(
                'INSERT INTO appointments (
                    customer_id,
                    service_id,
                    beautician_id,
                    appointment_date,
                    appointment_time,
                    notes
                )
                VALUES (
                    :customer_id,
                    :service_id,
                    :beautician_id,
                    :date,
                    :time,
                    :notes
                )'
            );

            $insert->execute([
                'customer_id' => (int) $customer['id'],
                'service_id' => $serviceId,
                'beautician_id' => $beauticianId,
                'date' => $date,
                'time' => $time . ':00',
                'notes' => $notes !== '' ? $notes : null
            ]);

            $_SESSION['success_message'] =
                'Your appointment was booked successfully. You can now pay online.';

            redirect('/customer/appointments.php');
        }
    }

    $pageTitle = 'Book an appointment';

    require __DIR__ . '/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <h1 class="h3">Book your salon visit</h1>

                    <p class="text-muted">
                        Choose your preferred service, beautician, date, and time.
                    </p>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= e($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$services || !$beauticians): ?>
                        <div class="alert alert-warning">
                            Booking will be available once the salon adds services
                            and beauticians.
                        </div>
                    <?php else: ?>

                        <form method="post" class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="service_id">
                                    Service
                                </label>

                                <select
                                    name="service_id"
                                    id="service_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select service</option>

                                    <?php foreach ($services as $service): ?>
                                        <option
                                            value="<?= (int) $service['id'] ?>"
                                            <?= $serviceId === (int) $service['id'] ? 'selected' : '' ?>
                                        >
                                            <?= e($service['service_name']) ?>
                                            —
                                            ₹<?= e(number_format((float) $service['price'], 2)) ?>
                                            (<?= (int) $service['duration'] ?> min)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="beautician_id">
                                    Beautician
                                </label>

                                <select
                                    name="beautician_id"
                                    id="beautician_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select beautician</option>

                                    <?php foreach ($beauticians as $beautician): ?>
                                        <option
                                            value="<?= (int) $beautician['id'] ?>"
                                            <?= $beauticianId === (int) $beautician['id'] ? 'selected' : '' ?>
                                        >
                                            <?= e($beautician['beautician_name']) ?>

                                            <?php if ($beautician['specialization']): ?>
                                                — <?= e($beautician['specialization']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="appointment_date">
                                    Date
                                </label>

                                <input
                                    type="date"
                                    id="appointment_date"
                                    name="appointment_date"
                                    min="<?= date('Y-m-d') ?>"
                                    class="form-control"
                                    value="<?= e($date) ?>"
                                    required
                                >
                            </div>

                            <?php if ($isBeauticianOnLeave): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">
                                        This beautician is on leave on the selected
                                        date. Please choose another beautician or date.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-6">
                                <label class="form-label" for="appointment_time">
                                    Available Time
                                </label>

                                <select
                                    name="appointment_time"
                                    id="appointment_time"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select time</option>

                                    <?php foreach ($salonSlots as $slot): ?>
                                        <?php
                                        $isBooked = in_array(
                                            $slot . ':00',
                                            $bookedTimes,
                                            true
                                        );
                                        ?>

                                        <option
                                            value="<?= e($slot) ?>"
                                            <?= $time === $slot ? 'selected' : '' ?>
                                            <?= ($isBooked || $isBeauticianOnLeave) ? 'disabled' : '' ?>
                                        >
                                            <?= e(date('h:i A', strtotime($slot))) ?>

                                            <?php if ($isBooked): ?>
                                                (Booked)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="notes">
                                    Note (optional)
                                </label>

                                <textarea
                                    name="notes"
                                    id="notes"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Anything the salon should know?"
                                ><?= e($notes) ?></textarea>
                            </div>

                            <div class="col-12">
                                <button
                                    type="submit"
                                    class="btn btn-primary px-4"
                                    <?= $isBeauticianOnLeave ? 'disabled' : '' ?>
                                >
                                    Confirm Booking
                                </button>
                            </div>

                        </form>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const beautician = document.getElementById('beautician_id');
    const appointmentDate = document.getElementById('appointment_date');

    function refreshAvailableSlots() {
        if (!beautician || !appointmentDate) {
            return;
        }

        if (!beautician.value || !appointmentDate.value) {
            return;
        }

        const url = new URL(window.location.href);

        url.searchParams.set('beautician_id', beautician.value);
        url.searchParams.set('appointment_date', appointmentDate.value);

        window.location.href = url.toString();
    }

    if (beautician && appointmentDate) {
        beautician.addEventListener('change', refreshAvailableSlots);
        appointmentDate.addEventListener('change', refreshAvailableSlots);
    }
</script>

<?php require __DIR__ . '/footer.php'; ?>
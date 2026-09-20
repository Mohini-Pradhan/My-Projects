<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireCustomer();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('/customer/appointments.php');
    }

    $appointmentId = (int) ($_POST['appointment_id'] ?? 0);

    if ($appointmentId <= 0) {
        $_SESSION['error_message'] = 'Invalid appointment.';
        redirect('/customer/appointments.php');
    }

    $cancel = $pdo->prepare(
        'UPDATE appointments
        INNER JOIN customers
            ON appointments.customer_id = customers.id
        SET appointments.status = "Cancelled"
        WHERE appointments.id = :appointment_id
        AND customers.user_id = :user_id
        AND appointments.status NOT IN ("Completed", "Cancelled")'
    );

    $cancel->execute([
        'appointment_id' => $appointmentId,
        'user_id' => (int) $_SESSION['user_id']
    ]);

    if ($cancel->rowCount() > 0) {
        $_SESSION['success_message'] = 'Your appointment has been cancelled.';
    } else {
        $_SESSION['error_message'] =
            'This appointment cannot be cancelled or does not belong to you.';
    }

    redirect('/customer/appointments.php');
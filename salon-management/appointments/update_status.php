<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('/appointments/index.php');
    }

    $appointmentId = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    $allowedStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

    if (
        !ctype_digit($appointmentId) ||
        !in_array($status, $allowedStatuses, true)
    ) {
        $_SESSION['error_message'] = 'Invalid appointment update.';
        redirect('/appointments/index.php');
    }

    $updateStatus = $pdo->prepare(
        'UPDATE appointments
        SET status = :status
        WHERE id = :id'
    );

    $updateStatus->execute([
        'status' => $status,
        'id' => (int) $appointmentId
    ]);

    if ($updateStatus->rowCount() > 0) {
        $_SESSION['success_message'] = 'Appointment status updated successfully.';
    } else {
        $_SESSION['error_message'] =
            'No status change was made, or the appointment was not found.';
    }

redirect('/appointments/index.php');

<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('/beauticians/index.php');
    }

    $beauticianId = $_POST['id'] ?? '';

    if (!ctype_digit($beauticianId)) {
        $_SESSION['error_message'] = 'Invalid beautician selected.';
        redirect('/beauticians/index.php');
    }

    $beauticianId = (int) $beauticianId;

    $appointmentCheck = $pdo->prepare(
        'SELECT COUNT(*)
        FROM appointments
        WHERE beautician_id = :id'
    );

    $appointmentCheck->execute(['id' => $beauticianId]);

    if ((int) $appointmentCheck->fetchColumn() > 0) {
        $_SESSION['error_message'] =
            'This beautician cannot be deleted because they have appointment records.';
        redirect('/beauticians/index.php');
    }

    $deleteBeautician = $pdo->prepare(
        'DELETE FROM beauticians
        WHERE id = :id'
    );

    $deleteBeautician->execute(['id' => $beauticianId]);

    if ($deleteBeautician->rowCount() === 1) {
        $_SESSION['success_message'] = 'Beautician deleted successfully.';
    } else {
        $_SESSION['error_message'] =
            'Beautician was not found or was already deleted.';
    }

redirect('/beauticians/index.php');

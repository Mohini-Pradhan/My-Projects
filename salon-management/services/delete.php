<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('/services/index.php');
    }

    $serviceId = $_POST['id'] ?? '';

    if (!ctype_digit($serviceId)) {
        $_SESSION['error_message'] = 'Invalid service selected.';
        redirect('/services/index.php');
    }

    $serviceId = (int) $serviceId;

    $appointmentCheck = $pdo->prepare(
        'SELECT COUNT(*)
        FROM appointments
        WHERE service_id = :id'
    );

    $appointmentCheck->execute(['id' => $serviceId]);

    if ((int) $appointmentCheck->fetchColumn() > 0) {
        $_SESSION['error_message'] =
            'This service cannot be deleted because it has appointment records.';
        redirect('/services/index.php');
    }

    $deleteService = $pdo->prepare(
        'DELETE FROM services
        WHERE id = :id'
    );

    $deleteService->execute(['id' => $serviceId]);

    if ($deleteService->rowCount() === 1) {
        $_SESSION['success_message'] = 'Service deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Service was not found or was already deleted.';
    }

    redirect('/services/index.php');

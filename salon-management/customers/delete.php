<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('/customers/index.php');
    }

    $customerId = $_POST['id'] ?? '';

    if (!ctype_digit($customerId)) {
        $_SESSION['error_message'] = 'Invalid customer selected.';
        redirect('/customers/index.php');
    }

    $customerId = (int) $customerId;

    $appointmentCheck = $pdo->prepare(
        'SELECT COUNT(*)
        FROM appointments
        WHERE customer_id = :id'
    );

    $appointmentCheck->execute(['id' => $customerId]);

    if ((int) $appointmentCheck->fetchColumn() > 0) {
        $_SESSION['error_message'] =
            'This customer cannot be deleted because they have appointment records.';
        redirect('/customers/index.php');
    }

    $deleteCustomer = $pdo->prepare(
        'DELETE FROM customers
        WHERE id = :id'
    );

    $deleteCustomer->execute(['id' => $customerId]);

    if ($deleteCustomer->rowCount() === 1) {
        $_SESSION['success_message'] = 'Customer deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Customer was not found or was already deleted.';
    }

redirect('/customers/index.php');

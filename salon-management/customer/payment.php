<?php declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireCustomer();
$appointmentId = (int) ($_GET['appointment'] ?? $_POST['appointment'] ?? 0);
$get = $pdo->prepare('SELECT a.id,a.status,s.service_name,s.price FROM appointments a JOIN customers c ON a.customer_id=c.id JOIN services s ON a.service_id=s.id WHERE a.id=:id AND c.user_id=:user_id');
$get->execute(['id' => $appointmentId, 'user_id' => (int) $_SESSION['user_id']]);
$appointment = $get->fetch();
if (!$appointment || $appointment['status'] === 'Cancelled')
    redirect('/customer/appointments.php');
$existing = $pdo->prepare('SELECT id FROM payments WHERE appointment_id=:id');
$existing->execute(['id' => $appointmentId]);
if ($existing->fetch())
    redirect('/customer/appointments.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_method'] ?? '';
    if (!in_array($method, ['Cash', 'Card', 'UPI'], true))
        $error = 'Please select a payment method.';
    else {
        $invoice = 'SAL-' . date('Ymd') . '-' . str_pad((string) $appointmentId, 4, '0', STR_PAD_LEFT);
        $payment = $pdo->prepare('INSERT INTO payments (appointment_id,amount,payment_method,payment_status,invoice_number,paid_at) VALUES (:appointment_id,:amount,:method,"Paid",:invoice,NOW())');
        $payment->execute(['appointment_id' => $appointmentId, 'amount' => $appointment['price'], 'method' => $method, 'invoice' => $invoice]);
        $_SESSION['success_message'] = 'Payment recorded successfully.';
        redirect('/customer/appointments.php');
    }
}
$pageTitle = 'Secure payment';
require __DIR__ . '/header.php'; ?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3">Complete payment</h1>
                    <div class="bg-light rounded p-3 mb-4"><strong><?= e($appointment['service_name']) ?></strong>
                        <div class="d-flex justify-content-between mt-2">
                            <span>Amount</span><strong>₹<?= e(number_format((float) $appointment['price'], 2)) ?></strong>
                        </div>
                    </div><?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                    <form method="post"><input type="hidden" name="appointment" value="<?= $appointmentId ?>"><label
                            class="form-label">Payment method</label><select class="form-select mb-4"
                            name="payment_method" required>
                            <option value="">Choose method</option>
                            <option>UPI</option>
                            <option>Card</option>
                            <option>Cash</option>
                        </select><button class="btn btn-primary w-100">Pay
                            ₹<?= e(number_format((float) $appointment['price'], 2)) ?></button></form>
                    <p class="small text-muted text-center mt-3 mb-0">Demo payment screen — connect a gateway before
                        accepting live payments.</p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/footer.php'; ?>
<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        $_SESSION['success_message'] = 'Invalid customer selected.';
        redirect('/customers/index.php');
    }

    $customerId = (int) $_GET['id'];

    $getCustomer = $pdo->prepare(
        'SELECT *
        FROM customers
        WHERE id = :id'
    );

    $getCustomer->execute(['id' => $customerId]);
    $customer = $getCustomer->fetch();

    if (!$customer) {
        $_SESSION['success_message'] = 'Customer not found.';
        redirect('/customers/index.php');
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customerName = trim($_POST['customer_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $gender = trim($_POST['gender'] ?? '');

        if ($customerName === '' || $phone === '') {
            $errors[] = 'Customer name and phone number are required.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        $allowedGenders = ['Female', 'Male', 'Other', 'Prefer not to say'];

        if ($gender !== '' && !in_array($gender, $allowedGenders, true)) {
            $errors[] = 'Please select a valid gender.';
        }

        if (empty($errors)) {
            $updateCustomer = $pdo->prepare(
                'UPDATE customers
                SET customer_name = :customer_name,
                    phone = :phone,
                    email = :email,
                    address = :address,
                    gender = :gender
                WHERE id = :id'
            );

            $updateCustomer->execute([
                'customer_name' => $customerName,
                'phone' => $phone,
                'email' => $email !== '' ? $email : null,
                'address' => $address !== '' ? $address : null,
                'gender' => $gender !== '' ? $gender : null,
                'id' => $customerId
            ]);

            $_SESSION['success_message'] = 'Customer updated successfully.';
            redirect('/customers/index.php');
        }

        $customer['customer_name'] = $customerName;
        $customer['phone'] = $phone;
        $customer['email'] = $email;
        $customer['address'] = $address;
        $customer['gender'] = $gender;
    }
?>

<?php
    $pageTitle = "Edit Customer";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h3 mb-4">Edit Customer</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="customer_name" class="form-label">
                                Customer Name *
                            </label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?= e($customer['customer_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= e($customer['phone']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= e($customer['email']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select gender</option>
                                <?php foreach (
                                    ['Female', 'Male', 'Other', 'Prefer not to say']
                                    as $option
                                ): ?>
                                    <option
                                        value="<?= e($option) ?>"
                                        <?= $customer['gender'] === $option ? 'selected' : '' ?>
                                    >
                                        <?= e($option) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= e($customer['address']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Customer
                        </button>

                        <a href="<?= BASE_URL ?>/customers/index.php" class="btn text-white" style="background-color: #c12f68;">
                            Cancel
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    requireAdmin();

    $errors = [];
    $customerName = '';
    $phone = '';
    $email = '';
    $address = '';
    $gender = '';

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
            $addCustomer = $pdo->prepare(
                'INSERT INTO customers
                (customer_name, phone, email, address, gender)
                VALUES
                (:customer_name, :phone, :email, :address, :gender)'
            );

            $addCustomer->execute([
                'customer_name' => $customerName,
                'phone' => $phone,
                'email' => $email !== '' ? $email : null,
                'address' => $address !== '' ? $address : null,
                'gender' => $gender !== '' ? $gender : null
            ]);

            $_SESSION['success_message'] = 'Customer added successfully.';
            redirect('/customers/index.php');
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    $errorMessage = $_SESSION['error_message'] ?? '';
    unset($_SESSION['error_message']);

    $search = trim($_GET['search'] ?? '');

    if ($search !== '') {
        $getCustomers = $pdo->prepare(
            'SELECT *
            FROM customers
            WHERE customer_name LIKE :name_search
                OR phone LIKE :phone_search
            ORDER BY id DESC'
        );

        $getCustomers->execute([
            'name_search' => '%' . $search . '%',
            'phone_search' => '%' . $search . '%'
        ]);
        $customers = $getCustomers->fetchAll();
    } else {
        $getCustomers = $pdo->query(
            'SELECT *
            FROM customers
            ORDER BY id DESC'
        );

        $customers = $getCustomers->fetchAll();
    }
?>

<?php
    $pageTitle = "Customers";
    require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-4">

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h4 mb-3">Add Customer</h1>

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
                            <input
                                type="text"
                                class="form-control"
                                id="customer_name"
                                name="customer_name"
                                value="<?= e($customerName) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                Phone *
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="phone"
                                name="phone"
                                value="<?= e($phone) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email
                            </label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= e($email) ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">
                                Gender
                            </label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select gender</option>
                                <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>
                                    Female
                                </option>
                                <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>
                                    Male
                                </option>
                                <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>
                                    Other
                                </option>
                                <option
                                    value="Prefer not to say"
                                    <?= $gender === 'Prefer not to say' ? 'selected' : '' ?>
                                >
                                    Prefer not to say
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">
                                Address
                            </label>
                            <textarea
                                class="form-control"
                                id="address"
                                name="address"
                                rows="3"
                            ><?= e($address) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Add Customer
                        </button>

                    </form>

                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 mb-0">Customer List</h2>
                        <span class="badge text-white" style="background-color: #4a0f2a;">
                            <?= count($customers) ?> Found
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

                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input
                                type="search"
                                class="form-control"
                                name="search"
                                placeholder="Search by customer name or phone"
                                value="<?= e($search) ?>"
                            >
                            <button class="btn text-white" style="background-color: #8f1d4c;" type="submit">
                                Search
                            </button>

                            <?php if ($search !== ''): ?>
                                <a
                                    href="<?= BASE_URL ?>/customers/index.php"
                                    class="btn btn-outline-secondary"
                                >
                                    Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Gender</th>
                                    <th>Added On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($customers)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No customers found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td><?= (int) $customer['id'] ?></td>
                                            <td><?= e($customer['customer_name']) ?></td>
                                            <td><?= e($customer['phone']) ?></td>
                                            <td><?= e($customer['email'] ?? '-') ?></td>
                                            <td><?= e($customer['gender'] ?? '-') ?></td>
                                            <td>
                                                <?= e(date(
                                                    'd M Y',
                                                    strtotime($customer['created_at'])
                                                )) ?>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="<?= BASE_URL ?>/customers/history.php?id=<?= (int) $customer['id'] ?>" class="btn text-white" style="background-color: #8f1d4c;">
                                                    History
                                                </a>
                                                <a href="<?= BASE_URL ?>/customers/edit.php?id=<?= (int) $customer['id'] ?>" class="btn text-white" style="background-color: #8f1d4c;">
                                                    Edit
                                                </a>
                                                <form method="POST" action="<?= BASE_URL ?>/customers/delete.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                    
                                                    <input type="hidden" name="id" value="<?= (int) $customer['id'] ?>">

                                                    <button type="submit" class="btn text-white" style="background-color: #8f1d4c;">
                                                        Delete
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

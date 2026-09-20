<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/auth.php';

    if (isLoggedIn()) {
        redirect(isCustomer() ? '/customer/index.php' : '/index.php');
    }

    $errors = [];

    $name = '';
    $email = '';
    $phone = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $password = $_POST['password'] ?? '';

        if (
            $name === '' ||
            $email === '' ||
            $phone === '' ||
            $gender === '' ||
            strlen($password) < 6
        ) {
            $errors[] = 'Please provide your name, phone, gender, a valid email, and a password of at least 6 characters.';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email.';
        }

        // Create account
        if (!$errors) {

            $check = $pdo->prepare(
                'SELECT id FROM users WHERE email = :email'
            );

            $check->execute([
                'email' => $email
            ]);

            if ($check->fetch()) {

                $errors[] = 'This email already has an account.';

            } else {

                $pdo->beginTransaction();

                try {

                    $add = $pdo->prepare(
                        'INSERT INTO users 
                        (name, email, phone, password, role)
                        VALUES 
                        (:name, :email, :phone, :password, "customer")'
                    );

                    $add->execute([
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'password' => password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        )
                    ]);

                    $customer = $pdo->prepare(
                        'INSERT INTO customers
                        (user_id, customer_name, phone, email, gender)
                        VALUES
                        (:user_id, :name, :phone, :email, :gender)'
                    );

                    $customer->execute([
                        'user_id' => (int) $pdo->lastInsertId(),
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'gender' => $gender !== '' ? $gender : null
                    ]);

                    $pdo->commit();

                    $_SESSION['success_message'] =
                        'Account created. Please log in to book your visit.';

                    redirect("/customer/login.php");

                } catch (Throwable $error) {

                    $pdo->rollBack();

                    $errors[] =
                        "We could not create your account. Please try again.";
                }
            }
        }
    }

    $pageTitle = 'Create account';

    require __DIR__ . '/header.php';

?>

<main class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-5">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-lg-5">

                    <h1 class="h3">
                        Create your customer account
                    </h1>

                    <p class="text-muted">
                        Book and manage your salon appointments online.
                    </p>

                    <?php if ($errors): ?>

                        <div class="alert alert-danger">

                            <?php foreach ($errors as $error): ?>

                                <div>
                                    <?= e($error) ?>
                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                    <form method="post">

                        <input class="form-control mb-3" name="name" placeholder="Full name" value="<?= e($name) ?>" required>

                        <input class="form-control mb-3" name="phone" placeholder="Phone number" value="<?= e($phone) ?>" required>

                        <select class="form-select mb-3" name="gender">
                            <option value="">Select gender</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                            <option value="Other">Other</option>
                            <option value="Prefer not to say">Prefer not to say</option>
                        </select>

                        <input class="form-control mb-3" type="email" name="email" placeholder="Email address" value="<?= e($email) ?>" required>

                        <input class="form-control mb-3" type="password" name="password" placeholder="Password (minimum 6 characters)" required>

                        <button type="submit" class="btn btn-primary w-100">
                            Create account
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0 small">

                        Already registered?

                        <a href="<?= BASE_URL ?>/customer/login.php">
                            Login
                        </a>

                    </p>

                </div>

            </div>

        </div>

    </div>

</main>

<?php require __DIR__ . '/footer.php'; ?>
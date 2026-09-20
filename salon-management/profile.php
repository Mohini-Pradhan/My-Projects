<?php
    declare(strict_types=1);

    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/includes/auth.php';

    requireLogin();

    $getUser = $pdo->prepare(
        'SELECT id, name, email, phone, image
        FROM users
        WHERE id = :id'
    );

    $getUser->execute([
        'id' => (int) $_SESSION['user_id']
    ]);

    $user = $getUser->fetch();

    if (!$user) {
        $_SESSION = [];
        session_destroy();
        redirect('/auth/login.php');
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        $imageName = $user['image'];

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Image upload failed. Please try again.';
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Image size must not be greater than 2 MB.';
            } else {
                $allowedImageTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file(
                    $fileInfo,
                    $_FILES['image']['tmp_name']
                );
                finfo_close($fileInfo);

                if (!isset($allowedImageTypes[$mimeType])) {
                    $errors[] =
                        'Upload only JPG, PNG, or WEBP image files.';
                } else {
                    $imageName =
                        'profile_' .
                        bin2hex(random_bytes(16)) .
                        '.' .
                        $allowedImageTypes[$mimeType];

                    $uploadPath = __DIR__ . '/uploads/' . $imageName;

                    if (!move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $uploadPath
                    )) {
                        $errors[] = 'Unable to save the uploaded image.';
                    }
                }
            }
        }

        if (empty($errors)) {
            $updateProfile = $pdo->prepare(
                'UPDATE users
                SET name = :name,
                    phone = :phone,
                    image = :image
                WHERE id = :id'
            );

            $updateProfile->execute([
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'image' => $imageName,
                'id' => (int) $user['id']
            ]);

            $_SESSION['user_name'] = $name;
            $_SESSION['success_message'] = 'Profile updated successfully.';

            redirect('/profile.php');
        }
    }

    $successMessage = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    $getUser->execute([
        'id' => (int) $_SESSION['user_id']
    ]);

    $user = $getUser->fetch();

$pageTitle = 'My Profile';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <?php if ($user['image']): ?>
                            <img
                                src="<?= BASE_URL ?>/uploads/<?= e($user['image']) ?>"
                                alt="Profile photo"
                                class="rounded-circle object-fit-cover"
                                width="80"
                                height="80"
                            >
                        <?php else: ?>
                            <div
                                class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3"
                                style="width: 80px; height: 80px;"
                            >
                                <?= e(strtoupper(substr($user['name'], 0, 1))) ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <h1 class="h3 mb-1">My Profile</h1>
                            <p class="text-muted mb-0">
                                Update your personal information and photo.
                            </p>
                        </div>

                    </div>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <?= e($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Full Name *
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?= e($user['name']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                value="<?= e($user['email']) ?>"
                                disabled
                            >

                            <div class="form-text">
                                Email cannot be changed from this page.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="phone"
                                name="phone"
                                value="<?= e($user['phone']) ?>"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">
                                Profile Image
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="image"
                                name="image"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <div class="form-text">
                                JPG, PNG, or WEBP only. Maximum size: 2 MB.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Profile
                        </button>

                        <a
                            href="<?= BASE_URL ?>/index.php"
                            class="btn text-white" style="background-color: #8f1d4c;"
                        >
                            Cancel
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
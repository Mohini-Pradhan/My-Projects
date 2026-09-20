<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$beauticians = $pdo->query(
    'SELECT id, beautician_name
     FROM beauticians
     ORDER BY beautician_name ASC'
)->fetchAll();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $beauticianId = (int) ($_POST['beautician_id'] ?? 0);
    $leaveDate = $_POST['leave_date'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    $dateObject = DateTime::createFromFormat('Y-m-d', $leaveDate);

    if (
        $beauticianId <= 0 ||
        !$dateObject ||
        $dateObject->format('Y-m-d') !== $leaveDate ||
        $leaveDate < date('Y-m-d')
    ) {
        $errors[] = 'Select a beautician and a valid future leave date.';
    }

    if (!$errors) {
        try {
            $addLeave = $pdo->prepare(
                'INSERT INTO beautician_leaves
                    (beautician_id, leave_date, reason)
                 VALUES
                    (:beautician_id, :leave_date, :reason)'
            );

            $addLeave->execute([
                'beautician_id' => $beauticianId,
                'leave_date' => $leaveDate,
                'reason' => $reason !== '' ? $reason : null
            ]);

            $success = 'Leave date added successfully.';
        } catch (PDOException $error) {
            $errors[] =
                'This beautician already has leave recorded for that date.';
        }
    }
}

$leaves = $pdo->query(
    'SELECT
        beautician_leaves.id,
        beautician_leaves.leave_date,
        beautician_leaves.reason,
        beauticians.beautician_name
     FROM beautician_leaves
     INNER JOIN beauticians
        ON beautician_leaves.beautician_id = beauticians.id
     ORDER BY beautician_leaves.leave_date ASC'
)->fetchAll();

$pageTitle = 'Beautician Leave';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">

    <div class="mb-4">
        <h1 class="h2 mb-1">Beautician Leave / Day Off</h1>
        <p class="text-muted mb-0">
            Set dates when a beautician cannot accept appointments.
        </p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Beautician</label>

                    <select
                        class="form-select"
                        name="beautician_id"
                        required
                    >
                        <option value="">Select beautician</option>

                        <?php foreach ($beauticians as $beautician): ?>
                            <option value="<?= (int) $beautician['id'] ?>">
                                <?= e($beautician['beautician_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Leave Date</label>

                    <input
                        type="date"
                        name="leave_date"
                        min="<?= date('Y-m-d') ?>"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Reason (optional)</label>

                    <input
                        type="text"
                        name="reason"
                        class="form-control"
                        placeholder="Example: Personal leave"
                    >
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">
                        Add Leave Date
                    </button>

                    <a
                        href="<?= BASE_URL ?>/beauticians/index.php"
                        class="btn btn-outline-secondary"
                    >
                        Back to Beauticians
                    </a>
                </div>

            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h2 class="h5 mb-3">Upcoming Leave Dates</h2>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Beautician</th>
                            <th>Leave Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!$leaves): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No leave dates added yet.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td><?= e($leave['beautician_name']) ?></td>
                                <td>
                                    <?= e(
                                        date(
                                            'd M Y',
                                            strtotime($leave['leave_date'])
                                        )
                                    ) ?>
                                </td>
                                <td><?= e($leave['reason'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";
    require_once "../includes/header.php";

    $search = trim($_GET["search"] ?? "");

    $allowedSorts = ["student_name", "email", "dob", "created_at"];
    $sort = in_array($_GET["sort"] ?? "", $allowedSorts) ? $_GET["sort"] : "id";
    $order = ($_GET["order"] ?? "DESC") === "ASC" ? "ASC" : "DESC";

    $sql = "SELECT * FROM students WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (student_name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY $sort $order";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    function sortLink($column, $label, $currentSort, $currentOrder, $search) {
        $newOrder = ($currentSort === $column && $currentOrder === "ASC") ? "DESC" : "ASC";
        $arrow = "";
        if ($currentSort === $column) {
            $arrow = $currentOrder === "ASC" ? " ▲" : " ▼";
        }
        $url = "?sort=" . $column . "&order=" . $newOrder . "&search=" . urlencode($search);
        return '<a href="' . htmlspecialchars($url) . '" class="text-white text-decoration-none">' . $label . $arrow . "</a>";
    }
?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Students</h2>
        <a href="add.php" class="btn btn-primary">+ Add Student</a>
    </div>

    <?php if (isset($_SESSION["success"])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION["success"]); unset($_SESSION["success"]); ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3 d-flex" style="max-width: 400px;">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="list.php" class="btn btn-outline-secondary ms-2">Clear</a>
        <?php endif; ?>
    </form>

    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th><?= sortLink('student_name', 'Name', $sort, $order, $search) ?></th>
                <th><?= sortLink("email", "Email", $sort, $order, $search) ?></th>
                <th>Phone</th>
                <th>Gender</th>
                <th><?= sortLink("dob", "DOB", $sort, $order, $search) ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($students): ?>
                <?php foreach ($students as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($s["student_name"]) ?></td>
                        <td><?= htmlspecialchars($s["email"]) ?></td>
                        <td><?= htmlspecialchars($s["phone"]) ?></td>
                        <td><?= htmlspecialchars($s["gender"]) ?></td>
                        <td><?= htmlspecialchars($s["dob"]) ?></td>
                        <td>
                            <a href="view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info">View</a>
                            <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No students found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

<?php require_once "../includes/footer.php"; ?>
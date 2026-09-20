<?php
include "../config/db.php";
include "../includes/auth.php";

$search = trim($_GET["search"] ?? "");
$sort = $_GET["sort"] ?? "latest";

$sort_options = [
    "name_asc" => "e.employee_name ASC",
    "name_desc" => "e.employee_name DESC",
    "salary_high" => "e.salary DESC",
    "salary_low" => "e.salary ASC",
    "latest" => "e.joining_date DESC",
    "oldest" => "e.joining_date ASC"
];

$order_by = $sort_options[$sort] ?? $sort_options["latest"];

if (!empty($search)) {

    $search_value = "%" . $search . "%";

    $query = mysqli_prepare(
        $conn,
        "SELECT
            e.*,
            d.department_name
         FROM employees e
         INNER JOIN departments d
         ON e.department_id = d.id
         WHERE
            e.employee_name LIKE ?
            OR e.email LIKE ?
            OR d.department_name LIKE ?
         ORDER BY $order_by"
    );

    mysqli_stmt_bind_param(
        $query,
        "sss",
        $search_value,
        $search_value,
        $search_value
    );

    mysqli_stmt_execute($query);
    $employees = mysqli_stmt_get_result($query);

} else {

    $employees = mysqli_query(
        $conn,
        "SELECT
            e.*,
            d.department_name
         FROM employees e
         INNER JOIN departments d
         ON e.department_id = d.id
         ORDER BY $order_by"
    );
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Employees</h2>

        <a href="create.php" class="btn btn-success">
            + Add Employee
        </a>
    </div>

    <?php if (isset($_GET["success"]) && $_GET["success"] == "created") { ?>
        <div class="alert alert-success">
            Employee added successfully.
        </div>
    <?php } ?>

    <?php if (isset($_GET["success"]) && $_GET["success"] == "updated") { ?>
        <div class="alert alert-success">
            Employee updated successfully.
        </div>
    <?php } ?>

    <?php if (isset($_GET["success"]) && $_GET["success"] == "deleted") { ?>
        <div class="alert alert-success">
            Employee deleted successfully.
        </div>
    <?php } ?>

    <form method="GET" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">

                <div class="col-md-5">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search name, email, or department"
                           value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="col-md-4">
                    <select name="sort" class="form-select">
                        <option value="latest" <?= $sort == "latest" ? "selected" : "" ?>>
                            Latest Joining Date
                        </option>

                        <option value="oldest" <?= $sort == "oldest" ? "selected" : "" ?>>
                            Oldest Joining Date
                        </option>

                        <option value="name_asc" <?= $sort == "name_asc" ? "selected" : "" ?>>
                            Employee Name A-Z
                        </option>

                        <option value="name_desc" <?= $sort == "name_desc" ? "selected" : "" ?>>
                            Employee Name Z-A
                        </option>

                        <option value="salary_high" <?= $sort == "salary_high" ? "selected" : "" ?>>
                            Highest Salary
                        </option>

                        <option value="salary_low" <?= $sort == "salary_low" ? "selected" : "" ?>>
                            Lowest Salary
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary">
                        Search / Sort
                    </button>

                    <a href="index.php" class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Employee Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Joining Date</th>
                        <th width="210">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($employees) > 0) { ?>

                        <?php while ($employee = mysqli_fetch_assoc($employees)) { ?>
                            <tr>
                                <td><?= $employee["id"] ?></td>

                                <td>
                                    <?= htmlspecialchars($employee["employee_name"]) ?>
                                </td>

                                <td><?= htmlspecialchars($employee["email"]) ?></td>

                                <td><?= htmlspecialchars($employee["phone"]) ?></td>

                                <td>
                                    <?= htmlspecialchars($employee["department_name"]) ?>
                                </td>

                                <td>
                                    ₹ <?= number_format($employee["salary"], 2) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($employee["joining_date"]) ?>
                                </td>

                                <td>
                                    <a href="view.php?id=<?= $employee["id"] ?>"
                                       class="btn btn-sm btn-info text-white">
                                        View
                                    </a>

                                    <a href="edit.php?id=<?= $employee["id"] ?>"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <a href="delete.php?id=<?= $employee["id"] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this employee?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No employees found.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
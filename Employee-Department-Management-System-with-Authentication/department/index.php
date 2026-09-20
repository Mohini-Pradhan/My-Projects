<?php
include "../config/db.php";
include "../includes/auth.php";

$departments = mysqli_query(
    $conn,
    "SELECT
        d.id,
        d.department_name,
        d.department_code,
        d.description,
        d.created_at,
        COUNT(e.id) AS total_employees
     FROM departments d
     LEFT JOIN employees e
     ON d.id = e.department_id
     GROUP BY d.id
     ORDER BY d.id DESC"
);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Departments</h2>

        <a href="create.php" class="btn btn-primary">
            + Add Department
        </a>
    </div>

    <?php if (isset($_GET["error"]) && $_GET["error"] == "has_employees") { ?>
        <div class="alert alert-danger">
            This department cannot be deleted because it has employees.
        </div>
    <?php } ?>

    <?php if (isset($_GET["success"]) && $_GET["success"] == "created") { ?>
        <div class="alert alert-success">
            Department added successfully.
        </div>
    <?php } ?>

    <?php if (isset($_GET["success"]) && $_GET["success"] == "updated") { ?>
        <div class="alert alert-success">
            Department updated successfully.
        </div>
    <?php } ?>

    <?php if (isset($_GET["success"]) && $_GET["success"] == "deleted") { ?>
        <div class="alert alert-success">
            Department deleted successfully.
        </div>
    <?php } ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Total Employees</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($departments) > 0) { ?>

                        <?php while ($department = mysqli_fetch_assoc($departments)) { ?>
                            <tr>
                                <td><?= $department["id"] ?></td>

                                <td>
                                    <?= htmlspecialchars($department["department_name"]) ?>
                                </td>

                                <td>                                    
                                    <?= htmlspecialchars($department["department_code"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($department["description"]) ?>
                                </td>

                                <td><?= $department["total_employees"] ?></td>

                                <td>
                                    <a href="edit.php?id=<?= $department["id"] ?>" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <a href="delete.php?id=<?= $department["id"] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this department?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No departments found.
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
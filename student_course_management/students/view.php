<?php
    require_once "../includes/auth_check.php";
    require_once "../config/db.php";

    $id = $_GET["id"] ?? null;
    if (!$id) { header("Location: list.php"); exit; }

    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();

    if (!$student) { header("Location: list.php"); exit; }

    require_once "../includes/header.php";
    ?>

    <h2 class="mb-4">Student Profile</h2>

    <div class="card shadow-sm" style="max-width: 600px;">
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($student['student_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['dob']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
            <p class="text-muted"><small>Registered on <?= $student['created_at'] ?></small></p>
        </div>
    </div>

    <a href="list.php" class="btn btn-secondary mt-3">Back to List</a>

    <?php require_once "../includes/footer.php"; ?>
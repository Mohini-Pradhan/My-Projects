<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employee Department Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light"></body>

<?php if (isset($_GET["success"]) && $_GET["success"] == "profile_updated") { ?>
    <div class="alert alert-success">
        Profile updated successfully. Please login again.
    </div>
<?php } ?>
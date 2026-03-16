<?php
// session_start();
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <title>Portfolio | Dashboard</title>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card p-4">
                    <h1>Portfolio Dashboard</h1>
                    <p class="lead">Welcome back! Manage your portfolio content below.</p>

                    <div class="d-grid gap-3 d-md-block">
                        <a href="admin.php" class="btn btn-outline-primary">Admin Profile</a>
                        <a href="skills.php" class="btn btn-outline-success">Skills / Tech Stack</a>
                        <a href="projects.php" class="btn btn-outline-dark">Projects</a>
                        <a href="certifications.php" class="btn btn-outline-warning">Certifications</a>
                        <a href="school_history.php" class="btn btn-outline-secondary">School History</a>
                        <a href="login.php" class="btn btn-outline-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
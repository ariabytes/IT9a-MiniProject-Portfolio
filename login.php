<?php
session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "portfolio_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if (isset($_POST['login'])) {
    $input_username = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password'];

    $result = $conn->query("SELECT * FROM admin WHERE username = '$input_username' LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($input_password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <title>Portfolio | Login</title>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-5 col-lg-4">

                <div class="card p-4">
                    <h1 class="mb-1">Admin Login</h1>
                    <p class="text-muted mb-4">Portfolio Management System</p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-3">
                    <a href="index.php" class="text-muted">← Back to Portfolio</a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>
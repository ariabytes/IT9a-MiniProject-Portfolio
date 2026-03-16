<?php
// session_start();
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "portfolio_db";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

$conn->query("CREATE TABLE IF NOT EXISTS admin (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(100) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    first_name      VARCHAR(100) NOT NULL,
    middle_initial  VARCHAR(5),
    last_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(150),
    github_url      VARCHAR(255),
    linkedin_url    VARCHAR(255),
    bio             TEXT,
    profile_pic     LONGBLOB,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// insert default admin if table is empty
$check = $conn->query("SELECT COUNT(*) AS total FROM admin");
$count = $check->fetch_assoc()['total'];
if ($count == 0) {
    $default_pass = password_hash("admin123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admin (username, password, first_name, last_name)
                  VALUES ('admin', '$default_pass', 'Your', 'Name')");
}

// LOAD CURRENT ADMIN DATA
$result = $conn->query("SELECT * FROM admin LIMIT 1");
$admin  = $result->fetch_assoc();

$edit_id             = $admin['id'];
$edit_username       = $admin['username'];
$edit_first_name     = $admin['first_name'];
$edit_middle_initial = $admin['middle_initial'];
$edit_last_name      = $admin['last_name'];
$edit_email          = $admin['email'];
$edit_github_url     = $admin['github_url'];
$edit_linkedin_url   = $admin['linkedin_url'];
$edit_bio            = $admin['bio'];

// UPDATE
if (isset($_POST['submit'])) {
    $id             = (int) $_POST['edit_id'];
    $username       = $conn->real_escape_string($_POST['username']);
    $first_name     = $conn->real_escape_string($_POST['first_name']);
    $middle_initial = $conn->real_escape_string($_POST['middle_initial']);
    $last_name      = $conn->real_escape_string($_POST['last_name']);
    $email          = $conn->real_escape_string($_POST['email']);
    $github_url     = $conn->real_escape_string($_POST['github_url']);
    $linkedin_url   = $conn->real_escape_string($_POST['linkedin_url']);
    $bio            = $conn->real_escape_string($_POST['bio']);

    if (!empty($_FILES['profile_pic']['name'])) {
        $pic_data = $conn->real_escape_string(file_get_contents($_FILES['profile_pic']['tmp_name']));
        $conn->query("UPDATE admin SET
            username        = '$username',
            first_name      = '$first_name',
            middle_initial  = '$middle_initial',
            last_name       = '$last_name',
            email           = '$email',
            github_url      = '$github_url',
            linkedin_url    = '$linkedin_url',
            bio             = '$bio',
            profile_pic     = '$pic_data'
            WHERE id = $id");
    } else {
        $conn->query("UPDATE admin SET
            username        = '$username',
            first_name      = '$first_name',
            middle_initial  = '$middle_initial',
            last_name       = '$last_name',
            email           = '$email',
            github_url      = '$github_url',
            linkedin_url    = '$linkedin_url',
            bio             = '$bio'
            WHERE id = $id");
    }

    if (!empty($_POST['new_password'])) {
        $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password = '$hashed' WHERE id = $id");
    }

    header("Location: admin.php?success=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <title>Portfolio | Admin</title>
</head>

<body>
    <a href="dashboard.php" class="btn btn-outline-secondary m-3">← Back to Dashboard</a>

    <div class="container mt-4">
        <h2 class="mb-4">Admin Profile</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Profile updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                history.replaceState(null, "", "admin.php");
            </script>
        <?php endif; ?>

        <div class="card p-4">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control mb-3" name="first_name" value="<?php echo htmlspecialchars($edit_first_name); ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">M.I.</label>
                        <input type="text" class="form-control mb-3" name="middle_initial" value="<?php echo htmlspecialchars($edit_middle_initial); ?>" maxlength="5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control mb-3" name="last_name" value="<?php echo htmlspecialchars($edit_last_name); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control mb-3" name="username" value="<?php echo htmlspecialchars($edit_username); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" class="form-control mb-3" name="new_password">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Email <small class="text-muted">(optional)</small></label>
                        <input type="email" class="form-control mb-3" name="email" value="<?php echo htmlspecialchars($edit_email); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">GitHub URL <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control mb-3" name="github_url" value="<?php echo htmlspecialchars($edit_github_url); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">LinkedIn URL <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control mb-3" name="linkedin_url" value="<?php echo htmlspecialchars($edit_linkedin_url); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bio</label>
                    <textarea class="form-control" name="bio" rows="4"><?php echo htmlspecialchars($edit_bio); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Picture</label><br>
                    <?php if (!empty($admin['profile_pic'])): ?>
                        <img src="data:image/png;base64,<?php echo base64_encode($admin['profile_pic']); ?>" width="80" class="mb-2 d-block rounded">
                    <?php endif; ?>
                    <input type="file" class="form-control" name="profile_pic" accept="image/*">
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>
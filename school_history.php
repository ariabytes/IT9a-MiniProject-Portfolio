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

$conn->query("CREATE TABLE IF NOT EXISTS school_history (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(200) NOT NULL,
    title       VARCHAR(150),
    year_start  YEAR,
    year_end    YEAR,
    achievements TEXT,
    image       VARCHAR(255)
)");

// create image folder if it doesn't exist
$upload_dir = "school_img/";
if (!is_dir($upload_dir)) mkdir($upload_dir);

// LOAD DATA FOR EDIT
$edit_id           = "";
$edit_school_name  = "";
$edit_title        = "";
$edit_year_start   = "";
$edit_year_end     = "";
$edit_achievements = "";

if (isset($_POST['edit_id']) && $_POST['edit_id'] !== '') {
    $edit_id = (int) $_POST['edit_id'];
    $result  = $conn->query("SELECT * FROM school_history WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $row               = $result->fetch_assoc();
        $edit_school_name  = $row['school_name'];
        $edit_title        = $row['title'];
        $edit_year_start   = $row['year_start'];
        $edit_year_end     = $row['year_end'];
        $edit_achievements = $row['achievements'];
    }
}

// SAVE/ADD
if (isset($_POST['save'])) {
    $school_name  = $conn->real_escape_string($_POST['school_name']);
    $title        = $conn->real_escape_string($_POST['title']);
    $year_start   = !empty($_POST['year_start']) ? (int) $_POST['year_start'] : NULL;
    $year_end     = !empty($_POST['year_end'])   ? (int) $_POST['year_end']   : NULL;
    $achievements = $conn->real_escape_string($_POST['achievements']);
    $image        = '';

    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
        $image = $conn->real_escape_string($image);
    }

    if ($image === '') {
        $conn->query("INSERT INTO school_history (school_name, title, year_start, year_end, achievements)
                      VALUES ('$school_name', '$title', '$year_start', '$year_end', '$achievements')");
    } else {
        $conn->query("INSERT INTO school_history (school_name, title, year_start, year_end, achievements, image)
                      VALUES ('$school_name', '$title', '$year_start', '$year_end', '$achievements', '$image')");
    }
    header("Location: school_history.php?success=added");
    exit();
}

// INSERT OR UPDATE
if (isset($_POST['submit'])) {
    $school_name  = $conn->real_escape_string($_POST['school_name']);
    $title        = $conn->real_escape_string($_POST['title']);
    $year_start   = !empty($_POST['year_start']) ? (int) $_POST['year_start'] : NULL;
    $year_end     = !empty($_POST['year_end'])   ? (int) $_POST['year_end']   : NULL;
    $achievements = $conn->real_escape_string($_POST['achievements']);
    $id           = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : "";

    if ($id == "") {
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            $image = $conn->real_escape_string($image);
        }
        if ($image === '') {
            $conn->query("INSERT INTO school_history (school_name, title, year_start, year_end, achievements)
                          VALUES ('$school_name', '$title', '$year_start', '$year_end', '$achievements')");
        } else {
            $conn->query("INSERT INTO school_history (school_name, title, year_start, year_end, achievements, image)
                          VALUES ('$school_name', '$title', '$year_start', '$year_end', '$achievements', '$image')");
        }
        header("Location: school_history.php?success=added");
        exit();
    } else {
        if (!empty($_FILES['image']['name'])) {
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            $image = $conn->real_escape_string($image);
            $conn->query("UPDATE school_history SET
                school_name  = '$school_name',
                title        = '$title',
                year_start   = '$year_start',
                year_end     = '$year_end',
                achievements = '$achievements',
                image        = '$image'
                WHERE id = $id");
        } else {
            $conn->query("UPDATE school_history SET
                school_name  = '$school_name',
                title        = '$title',
                year_start   = '$year_start',
                year_end     = '$year_end',
                achievements = '$achievements'
                WHERE id = $id");
        }
        header("Location: school_history.php?success=updated");
        exit();
    }
}

// DELETE
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $conn->query("DELETE FROM school_history WHERE id = $id");
    header("Location: school_history.php?success=deleted");
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
    <title>Portfolio | School History</title>
</head>

<body>
    <a href="dashboard.php" class="btn btn-outline-secondary m-3">← Back to Dashboard</a>

    <div class="container mt-4">
        <h2 class="mb-4">School History</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['success'] === 'added')   echo "School record added successfully!";
                if ($_GET['success'] === 'updated') echo "School record updated successfully!";
                if ($_GET['success'] === 'deleted') echo "School record deleted successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                history.replaceState(null, "", "school_history.php");
            </script>
        <?php endif; ?>

        <!-- FORM -->
        <div class="card p-3">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">School Name</label>
                        <input type="text" class="form-control mb-3" name="school_name" value="<?php echo htmlspecialchars($edit_school_name); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Degree / Strand / Title <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control mb-3" name="title" value="<?php echo htmlspecialchars($edit_title); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Year Start <small class="text-muted">(optional)</small></label>
                        <input type="number" class="form-control mb-3" name="year_start" value="<?php echo $edit_year_start; ?>" min="1990" max="2099">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year End <small class="text-muted">(leave blank if current)</small></label>
                        <input type="number" class="form-control mb-3" name="year_end" value="<?php echo $edit_year_end; ?>" min="1990" max="2099">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">School Logo / Image <small class="text-muted">(optional)</small></label>
                        <input type="file" class="form-control mb-3" name="image" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Achievements / Honors <small class="text-muted">(optional, comma-separated)</small></label>
                    <textarea class="form-control" name="achievements" rows="3"><?php echo htmlspecialchars($edit_achievements); ?></textarea>
                </div>

                <button type="submit" name="<?php echo $edit_id ? 'submit' : 'save'; ?>" class="btn btn-primary">
                    <?php echo $edit_id ? 'Update' : 'Save'; ?>
                </button>
                <?php if ($edit_id): ?>
                    <a href="school_history.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABLE -->
        <div class="mt-4">
            <table class="table table-bordered mt-2 text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>School Name</th>
                        <th>Degree / Title</th>
                        <th>Year Start</th>
                        <th>Year End</th>
                        <th>Achievements</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM school_history");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>";
                        if (!empty($row['image']) && $row['image'] !== 'NULL') {
                            echo "<img src='school_img/" . htmlspecialchars($row['image']) . "' width='50' height='50' style='object-fit:cover;'>";
                        } else {
                            echo "<img src='default_img.png' width='50' height='50'>";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['school_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . ($row['year_start'] ?? '—') . "</td>";
                        echo "<td>" . ($row['year_end']   ?? 'Present') . "</td>";
                        echo "<td>" . htmlspecialchars($row['achievements']) . "</td>";
                        echo "<td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='edit_id' value='" . $row['id'] . "'>
                                <button type='submit' class='btn btn-sm btn-outline-secondary'>Edit</button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                                <button type='submit' class='btn btn-sm btn-outline-danger'>Delete</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>
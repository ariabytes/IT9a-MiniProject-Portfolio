<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

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

$conn->query("CREATE TABLE IF NOT EXISTS skills (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL UNIQUE,
    category   VARCHAR(100),
    photo      LONGBLOB
)");

// LOAD DATA FOR EDIT
$edit_id         = "";
$edit_skill_name = "";
$edit_category   = "";

if (isset($_POST['edit_id']) && $_POST['edit_id'] !== '') {
    $edit_id = (int) $_POST['edit_id'];
    $result  = $conn->query("SELECT * FROM skills WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $row             = $result->fetch_assoc();
        $edit_skill_name = $row['skill_name'];
        $edit_category   = $row['category'];
    }
}

// SAVE/ADD
if (isset($_POST['save'])) {
    $skill_name = $conn->real_escape_string($_POST['skill_name']);
    $category   = $conn->real_escape_string($_POST['category']);

    if (!empty($_FILES['photo']['name'])) {
        $photo = $conn->real_escape_string(file_get_contents($_FILES['photo']['tmp_name']));
        $conn->query("INSERT INTO skills (skill_name, category, photo) VALUES ('$skill_name', '$category', '$photo')");
    } else {
        $conn->query("INSERT INTO skills (skill_name, category) VALUES ('$skill_name', '$category')");
    }

    header("Location: skills.php?success=added");
    exit();
}

// INSERT OR UPDATE
if (isset($_POST['submit'])) {
    $skill_name = $conn->real_escape_string($_POST['skill_name']);
    $category   = $conn->real_escape_string($_POST['category']);
    $id         = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : "";

    if ($id == "") {
        // INSERT
        if (!empty($_FILES['photo']['name'])) {
            $photo = $conn->real_escape_string(file_get_contents($_FILES['photo']['tmp_name']));
            $conn->query("INSERT INTO skills (skill_name, category, photo) VALUES ('$skill_name', '$category', '$photo')");
        } else {
            $conn->query("INSERT INTO skills (skill_name, category) VALUES ('$skill_name', '$category')");
        }
        header("Location: skills.php?success=added");
        exit();
    } else {
        // UPDATE
        if (!empty($_FILES['photo']['name'])) {
            $photo = $conn->real_escape_string(file_get_contents($_FILES['photo']['tmp_name']));
            $conn->query("UPDATE skills SET
                skill_name = '$skill_name',
                category   = '$category',
                photo      = '$photo'
                WHERE id = $id");
        } else {
            $conn->query("UPDATE skills SET
                skill_name = '$skill_name',
                category   = '$category'
                WHERE id = $id");
        }
        header("Location: skills.php?success=updated");
        exit();
    }
}

// DELETE
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $conn->query("DELETE FROM skills WHERE id = $id");
    header("Location: skills.php?success=deleted");
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
    <title>Portfolio | Skills</title>
</head>

<body>
    <a href="dashboard.php" class="btn btn-outline-secondary m-3">← Back to Dashboard</a>

    <div class="container mt-4">
        <h2 class="mb-4">Skills / Tech Stack</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['success'] === 'added')   echo "Skill added successfully!";
                if ($_GET['success'] === 'updated') echo "Skill updated successfully!";
                if ($_GET['success'] === 'deleted') echo "Skill deleted successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                history.replaceState(null, "", "skills.php");
            </script>
        <?php endif; ?>

        <!-- FORM -->
        <div class="card p-3">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Skill Name</label>
                        <input type="text" class="form-control mb-3" name="skill_name" value="<?php echo htmlspecialchars($edit_skill_name); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select class="form-select mb-3" name="category">
                            <option value="">— Select Category —</option>
                            <option value="Languages" <?php echo $edit_category === 'Languages'  ? 'selected' : ''; ?>>Languages</option>
                            <option value="Framework / Library" <?php echo $edit_category === 'Framework / Library'   ? 'selected' : ''; ?>>Framework / Library</option>
                            <option value="Database" <?php echo $edit_category === 'Database' ? 'selected' : ''; ?>>Database</option>
                            <option value="Hardware / Networking" <?php echo $edit_category === 'Hardware / Networking' ? 'selected' : ''; ?>>Hardware / Networking</option>
                            <option value="Software / Tools" <?php echo $edit_category === 'Software / Tools'      ? 'selected' : ''; ?>>Software / Tools</option>
                            <option value="Design" <?php echo $edit_category === 'Design' ? 'selected' : ''; ?>>Design</option>
                            <option value="AI Tools" <?php echo $edit_category === 'AI Tools' ? 'selected' : ''; ?>>AI Tools</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Logo / Icon <small class="text-muted">(optional)</small></label>
                        <input type="file" class="form-control mb-3" name="photo" accept="image/*">
                    </div>
                </div>

                <button type="submit" name="<?php echo $edit_id ? 'submit' : 'save'; ?>" class="btn btn-primary">
                    <?php echo $edit_id ? 'Update' : 'Save'; ?>
                </button>
                <?php if ($edit_id): ?>
                    <a href="skills.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABLE -->
        <div class="mt-4">
            <table class="table table-bordered mt-2 text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Skill Name</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM skills ORDER BY category, skill_name");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>";
                        if (!empty($row['photo'])) {
                            echo "<img src='data:image/png;base64," . base64_encode($row['photo']) . "' width='50' height='50'>";
                        } else {
                            echo "<img src='default_img.png' width='50' height='50'>";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['skill_name']) . "</td>";
                        echo "<td>" . ($row['category'] ?? '—') . "</td>";
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
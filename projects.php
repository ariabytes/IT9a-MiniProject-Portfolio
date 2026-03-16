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
    photo      LONGBLOB
)");

$conn->query("CREATE TABLE IF NOT EXISTS projects (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(150) NOT NULL,
    description  TEXT,
    link         VARCHAR(255),
    link2        VARCHAR(255),
    skill_ids    VARCHAR(255),
    photo        VARCHAR(255),
    date_created DATE
)");

// create image folder if it doesn't exist
$upload_dir = "projects_img/";
if (!is_dir($upload_dir)) mkdir($upload_dir);

// LOAD DATA FOR EDIT
$edit_id           = "";
$edit_title        = "";
$edit_description  = "";
$edit_link         = "";
$edit_link2        = "";
$edit_skill_ids    = [];
$edit_date_created = "";

if (isset($_POST['edit_id']) && $_POST['edit_id'] !== '') {
    $edit_id = (int) $_POST['edit_id'];
    $result  = $conn->query("SELECT * FROM projects WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $row               = $result->fetch_assoc();
        $edit_title        = $row['title'];
        $edit_description  = $row['description'];
        $edit_link         = $row['link'];
        $edit_link2        = $row['link2'];
        $edit_skill_ids    = !empty($row['skill_ids']) ? explode(',', $row['skill_ids']) : [];
        $edit_date_created = $row['date_created'];
    }
}

// SAVE/ADD
if (isset($_POST['save'])) {
    $title        = $conn->real_escape_string($_POST['title']);
    $description  = $conn->real_escape_string($_POST['description']);
    $link         = $conn->real_escape_string($_POST['link']);
    $link2        = $conn->real_escape_string($_POST['link2']);
    $skill_ids    = isset($_POST['skill_ids']) ? implode(',', array_map('intval', $_POST['skill_ids'])) : '';
    $date_created = !empty($_POST['date_created']) ? $_POST['date_created'] : NULL;
    $photo        = '';

    if (!empty($_FILES['photo']['name'])) {
        $photo = basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
        $photo = $conn->real_escape_string($photo);
    }

    if ($photo === '') {
        $conn->query("INSERT INTO projects (title, description, link, link2, skill_ids, date_created)
                      VALUES ('$title', '$description', '$link', '$link2', '$skill_ids', '$date_created')");
    } else {
        $conn->query("INSERT INTO projects (title, description, link, link2, skill_ids, photo, date_created)
                      VALUES ('$title', '$description', '$link', '$link2', '$skill_ids', '$photo', '$date_created')");
    }
    header("Location: projects.php?success=added");
    exit();
}

// INSERT OR UPDATE
if (isset($_POST['submit'])) {
    $title        = $conn->real_escape_string($_POST['title']);
    $description  = $conn->real_escape_string($_POST['description']);
    $link         = $conn->real_escape_string($_POST['link']);
    $link2        = $conn->real_escape_string($_POST['link2']);
    $skill_ids    = isset($_POST['skill_ids']) ? implode(',', array_map('intval', $_POST['skill_ids'])) : '';
    $date_created = !empty($_POST['date_created']) ? $_POST['date_created'] : NULL;
    $id           = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : "";

    if ($id == "") {
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $photo = basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
            $photo = $conn->real_escape_string($photo);
        }
        if ($photo === '') {
            $conn->query("INSERT INTO projects (title, description, link, link2, skill_ids, date_created)
                          VALUES ('$title', '$description', '$link', '$link2', '$skill_ids', '$date_created')");
        } else {
            $conn->query("INSERT INTO projects (title, description, link, link2, skill_ids, photo, date_created)
                          VALUES ('$title', '$description', '$link', '$link2', '$skill_ids', '$photo', '$date_created')");
        }
        header("Location: projects.php?success=added");
        exit();
    } else {
        if (!empty($_FILES['photo']['name'])) {
            $photo = basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
            $photo = $conn->real_escape_string($photo);
            $conn->query("UPDATE projects SET
                title        = '$title',
                description  = '$description',
                link         = '$link',
                link2        = '$link2',
                skill_ids    = '$skill_ids',
                photo        = '$photo',
                date_created = '$date_created'
                WHERE id = $id");
        } else {
            $conn->query("UPDATE projects SET
                title        = '$title',
                description  = '$description',
                link         = '$link',
                link2        = '$link2',
                skill_ids    = '$skill_ids',
                date_created = '$date_created'
                WHERE id = $id");
        }
        header("Location: projects.php?success=updated");
        exit();
    }
}

// DELETE
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $conn->query("DELETE FROM projects WHERE id = $id");
    header("Location: projects.php?success=deleted");
    exit();
}

// fetch all skills for dropdown
$all_skills = [];
$skills_result = $conn->query("SELECT * FROM skills ORDER BY skill_name");
while ($s = $skills_result->fetch_assoc()) {
    $all_skills[] = $s;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <title>Portfolio | Projects</title>
</head>

<body>
    <a href="dashboard.php" class="btn btn-outline-secondary m-3">← Back to Dashboard</a>

    <div class="container mt-4">
        <h2 class="mb-4">Projects</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['success'] === 'added')   echo "Project added successfully!";
                if ($_GET['success'] === 'updated') echo "Project updated successfully!";
                if ($_GET['success'] === 'deleted') echo "Project deleted successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                history.replaceState(null, "", "projects.php");
            </script>
        <?php endif; ?>

        <!-- FORM -->
        <div class="card p-3">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Project Title</label>
                        <input type="text" class="form-control mb-3" name="title" value="<?php echo htmlspecialchars($edit_title); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Skill / Tech Stack <small class="text-muted">(optional)</small></label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle-btn dropdown-toggle mb-3" type="button" id="skillDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <?php
                                if (!empty($edit_skill_ids)) {
                                    $selected_names = [];
                                    foreach ($all_skills as $s) {
                                        if (in_array($s['id'], $edit_skill_ids)) {
                                            $selected_names[] = htmlspecialchars($s['skill_name']);
                                        }
                                    }
                                    echo count($selected_names) > 0 ? implode(', ', $selected_names) : 'Select Skills';
                                } else {
                                    echo 'Select Skills';
                                }
                                ?>
                            </button>
                            <div class="dropdown-menu dropdown-checkbox-menu" aria-labelledby="skillDropdown">
                                <?php if (!empty($all_skills)): ?>
                                    <?php foreach ($all_skills as $s): ?>
                                        <div class="form-check">
                                            <input class="form-check-input skill-checkbox" type="checkbox"
                                                name="skill_ids[]"
                                                value="<?php echo $s['id']; ?>"
                                                id="skill_<?php echo $s['id']; ?>"
                                                <?php echo in_array($s['id'], $edit_skill_ids) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="skill_<?php echo $s['id']; ?>">
                                                <?php echo htmlspecialchars($s['skill_name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted px-2">No skills added yet</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">GitHub / Primary Link <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control mb-3" name="link" value="<?php echo htmlspecialchars($edit_link); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Figma / Secondary Link <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control mb-3" name="link2" value="<?php echo htmlspecialchars($edit_link2); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Project Screenshot <small class="text-muted">(optional)</small></label>
                        <input type="file" class="form-control mb-3" name="photo" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date Created <small class="text-muted">(optional)</small></label>
                        <input type="date" class="form-control mb-3" name="date_created" value="<?php echo $edit_date_created; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($edit_description); ?></textarea>
                </div>

                <button type="submit" name="<?php echo $edit_id ? 'submit' : 'save'; ?>" class="btn btn-primary">
                    <?php echo $edit_id ? 'Update' : 'Save'; ?>
                </button>
                <?php if ($edit_id): ?>
                    <a href="projects.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABLE -->
        <div class="mt-4">
            <table class="table table-bordered mt-2 text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Title</th>
                        <th>Skills</th>
                        <th>Primary Link</th>
                        <th>Secondary Link</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM projects ORDER BY date_created DESC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>";
                        if (!empty($row['photo'])) {
                            echo "<img src='projects_img/" . htmlspecialchars($row['photo']) . "' width='60' height='50' style='object-fit:cover;'>";
                        } else {
                            echo "<img src='default_img.png' width='60' height='50'>";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";

                        // display skill names from comma-separated IDs
                        echo "<td>";
                        if (!empty($row['skill_ids'])) {
                            $ids = explode(',', $row['skill_ids']);
                            $ids_str = implode(',', array_map('intval', $ids));
                            $skill_names_result = $conn->query("SELECT skill_name FROM skills WHERE id IN ($ids_str)");
                            $names = [];
                            while ($sn = $skill_names_result->fetch_assoc()) {
                                $names[] = htmlspecialchars($sn['skill_name']);
                            }
                            echo implode(', ', $names);
                        } else {
                            echo "—";
                        }
                        echo "</td>";

                        echo "<td>";
                        if (!empty($row['link'])) {
                            echo "<a href='" . htmlspecialchars($row['link']) . "' target='_blank'>View</a>";
                        } else {
                            echo "—";
                        }
                        echo "</td>";
                        echo "<td>";
                        if (!empty($row['link2'])) {
                            echo "<a href='" . htmlspecialchars($row['link2']) . "' target='_blank'>View</a>";
                        } else {
                            echo "—";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . (!empty($row['date_created']) ? date('F Y', strtotime($row['date_created'])) : '—') . "</td>";
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
    <script src="scripts/script.js"></script>
</body>

</html>
<?php $conn->close(); ?>
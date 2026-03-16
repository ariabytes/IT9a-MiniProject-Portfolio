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

$conn->query("CREATE TABLE IF NOT EXISTS certifications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    issuing_org VARCHAR(150),
    issued_date DATE,
    description TEXT,
    image       VARCHAR(255)
)");

// create image folder if it doesn't exist
$upload_dir = "certificate_img/";
if (!is_dir($upload_dir)) mkdir($upload_dir);

// LOAD DATA FOR EDIT
$edit_id          = "";
$edit_title       = "";
$edit_issuing_org = "";
$edit_issued_date = "";
$edit_description = "";

if (isset($_POST['edit_id']) && $_POST['edit_id'] !== '') {
    $edit_id = (int) $_POST['edit_id'];
    $result  = $conn->query("SELECT * FROM certifications WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $row              = $result->fetch_assoc();
        $edit_title       = $row['title'];
        $edit_issuing_org = $row['issuing_org'];
        $edit_issued_date = $row['issued_date'];
        $edit_description = $row['description'];
    }
}

// SAVE/ADD
if (isset($_POST['save'])) {
    $title       = $conn->real_escape_string($_POST['title']);
    $issuing_org = $conn->real_escape_string($_POST['issuing_org']);
    $issued_date = !empty($_POST['issued_date']) ? $_POST['issued_date'] : NULL;
    $description = $conn->real_escape_string($_POST['description']);
    $image       = '';

    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
        $image = $conn->real_escape_string($image);
    }

    if ($image === '') {
        $conn->query("INSERT INTO certifications (title, issuing_org, issued_date, description)
                      VALUES ('$title', '$issuing_org', '$issued_date', '$description')");
    } else {
        $conn->query("INSERT INTO certifications (title, issuing_org, issued_date, description, image)
                      VALUES ('$title', '$issuing_org', '$issued_date', '$description', '$image')");
    }
    header("Location: certifications.php?success=added");
    exit();
}

// INSERT OR UPDATE
if (isset($_POST['submit'])) {
    $title       = $conn->real_escape_string($_POST['title']);
    $issuing_org = $conn->real_escape_string($_POST['issuing_org']);
    $issued_date = !empty($_POST['issued_date']) ? $_POST['issued_date'] : NULL;
    $description = $conn->real_escape_string($_POST['description']);
    $id          = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : "";

    if ($id == "") {
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            $image = $conn->real_escape_string($image);
        }
        if ($image === '') {
            $conn->query("INSERT INTO certifications (title, issuing_org, issued_date, description)
                          VALUES ('$title', '$issuing_org', '$issued_date', '$description')");
        } else {
            $conn->query("INSERT INTO certifications (title, issuing_org, issued_date, description, image)
                          VALUES ('$title', '$issuing_org', '$issued_date', '$description', '$image')");
        }
        header("Location: certifications.php?success=added");
        exit();
    } else {
        if (!empty($_FILES['image']['name'])) {
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
            $image = $conn->real_escape_string($image);
            $conn->query("UPDATE certifications SET
                title       = '$title',
                issuing_org = '$issuing_org',
                issued_date = '$issued_date',
                description = '$description',
                image       = '$image'
                WHERE id = $id");
        } else {
            $conn->query("UPDATE certifications SET
                title       = '$title',
                issuing_org = '$issuing_org',
                issued_date = '$issued_date',
                description = '$description'
                WHERE id = $id");
        }
        header("Location: certifications.php?success=updated");
        exit();
    }
}

// DELETE
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    $conn->query("DELETE FROM certifications WHERE id = $id");
    header("Location: certifications.php?success=deleted");
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
    <title>Portfolio | Certifications</title>
</head>

<body>
    <a href="dashboard.php" class="btn btn-outline-secondary m-3">← Back to Dashboard</a>

    <div class="container mt-4">
        <h2 class="mb-4">Certifications</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['success'] === 'added')   echo "Certification added successfully!";
                if ($_GET['success'] === 'updated') echo "Certification updated successfully!";
                if ($_GET['success'] === 'deleted') echo "Certification deleted successfully!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                history.replaceState(null, "", "certifications.php");
            </script>
        <?php endif; ?>

        <!-- FORM -->
        <div class="card p-3">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control mb-3" name="title" value="<?php echo htmlspecialchars($edit_title); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Issuing Organization <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control mb-3" name="issuing_org" value="<?php echo htmlspecialchars($edit_issuing_org); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Issued Date <small class="text-muted">(optional)</small></label>
                        <input type="date" class="form-control mb-3" name="issued_date" value="<?php echo $edit_issued_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image <small class="text-muted">(optional)</small></label>
                        <input type="file" class="form-control mb-3" name="image" accept="image/*">
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
                    <a href="certifications.php" class="btn btn-outline-secondary">Cancel</a>
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
                        <th>Title</th>
                        <th>Issuing Org</th>
                        <th>Issued Date</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM certifications");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>";
                        if (!empty($row['image'])) {
                            echo "<img src='certificate_img/" . htmlspecialchars($row['image']) . "' width='50' height='50' style='object-fit:cover;'>";
                        } else {
                            echo "<img src='default_img.png' width='50' height='50'>";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['issuing_org']) . "</td>";
                        echo "<td>" . ($row['issued_date'] ?? '—') . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
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
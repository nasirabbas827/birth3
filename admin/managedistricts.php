<?php
session_start(); // Ensure the session is started
$title = "Manage Districts";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Initialize message variables
$message = "";

// Handle district addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_district'])) {
    $districtName = trim($_POST['district_name']);
    
    if (!empty($districtName)) {
        // Insert new district
        $stmt = $conn->prepare("INSERT INTO Districts (DistrictName) VALUES (?)");
        $stmt->bind_param("s", $districtName);
        
        if ($stmt->execute()) {
            $message = "District added successfully.";
        } else {
            $message = "Error adding district: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a district name.";
    }
}

// Handle district deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Districts WHERE DistrictID = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        $message = "District deleted successfully.";
    } else {
        $message = "Error deleting district: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all districts
$query = "SELECT DistrictID, DistrictName FROM Districts";
$result = $conn->query($query);
?>

<main class="container my-4" style="height:100vh;">
    <h1 class="m-4 text-center">Manage Districts</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add District Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="district_name" class="form-label">District Name<span class="text-danger">*</span></label>
            <input type="text" name="district_name" class="form-control" id="district_name" required>
        </div>
        <button type="submit" name="add_district" class="btn btn-primary">Add District</button>
    </form>

    <h2 class="text-center">Existing Districts</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>District ID</th>
                    <th>District Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($district = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($district['DistrictID']) ?></td>
                        <td><?= htmlspecialchars($district['DistrictName']) ?></td>
                        <td>
                            <a href="editdistrict.php?id=<?= $district['DistrictID'] ?>" class="btn btn-warning">Edit</a>
                            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?delete_id=<?= $district['DistrictID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this district?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h3>No districts found.</h3>
        </div>
    <?php endif; ?>

    <?php
    // Free result set
    $result->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

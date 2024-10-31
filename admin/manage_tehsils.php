<?php
session_start(); // Ensure the session is started
$title = "Manage Tehsils";
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

// Handle tehsil addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_tehsil'])) {
    $tehsilName = trim($_POST['tehsil_name']);
    $districtId = intval($_POST['district_id']);
    
    if (!empty($tehsilName) && $districtId > 0) {
        // Insert new tehsil
        $stmt = $conn->prepare("INSERT INTO Tehsils (TehsilName, DistrictID) VALUES (?, ?)");
        $stmt->bind_param("si", $tehsilName, $districtId);
        
        if ($stmt->execute()) {
            $message = "Tehsil added successfully.";
        } else {
            $message = "Error adding tehsil: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a tehsil name and select a district.";
    }
}

// Handle tehsil deletion
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM Tehsils WHERE TehsilID = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        $message = "Tehsil deleted successfully.";
    } else {
        $message = "Error deleting tehsil: " . $stmt->error;
    }
    $stmt->close();
}

// Handle tehsil editing
if (isset($_POST['edit_tehsil'])) {
    $tehsilId = intval($_POST['tehsil_id']);
    $tehsilName = trim($_POST['tehsil_name']);
    $districtId = intval($_POST['district_id']);

    if (!empty($tehsilName) && $districtId > 0) {
        $stmt = $conn->prepare("UPDATE Tehsils SET TehsilName = ?, DistrictID = ? WHERE TehsilID = ?");
        $stmt->bind_param("sii", $tehsilName, $districtId, $tehsilId);
        
        if ($stmt->execute()) {
            $message = "Tehsil updated successfully.";
        } else {
            $message = "Error updating tehsil: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a tehsil name and select a district.";
    }
}

// Fetch all tehsils
$query = "SELECT t.TehsilID, t.TehsilName, d.DistrictName FROM Tehsils t JOIN Districts d ON t.DistrictID = d.DistrictID";
$result = $conn->query($query);

// Fetch all districts for dropdown
$districtsQuery = "SELECT DistrictID, DistrictName FROM Districts";
$districtsResult = $conn->query($districtsQuery);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Manage Tehsils</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Tehsil Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="tehsil_name" class="form-label">Tehsil Name<span class="text-danger">*</span></label>
            <input type="text" name="tehsil_name" class="form-control" id="tehsil_name" required>
        </div>
        <div class="mb-3">
            <label for="district_id" class="form-label">Select District<span class="text-danger">*</span></label>
            <select name="district_id" class="form-control" id="district_id" required>
                <option value="">Select District</option>
                <?php while ($district = $districtsResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($district['DistrictID']) ?>"><?= htmlspecialchars($district['DistrictName']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="add_tehsil" class="btn btn-primary">Add Tehsil</button>
    </form>

    <h2 class="text-center">Existing Tehsils</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tehsil ID</th>
                    <th>Tehsil Name</th>
                    <th>District Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tehsil = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tehsil['TehsilID']) ?></td>
                        <td><?= htmlspecialchars($tehsil['TehsilName']) ?></td>
                        <td><?= htmlspecialchars($tehsil['DistrictName']) ?></td>
                        <td>
                            <a href="edittehsil.php?id=<?= $tehsil['TehsilID'] ?>" class="btn btn-warning">Edit</a>
                            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?delete_id=<?= $tehsil['TehsilID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this tehsil?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h3>No tehsils found.</h3>
        </div>
    <?php endif; ?>

    <?php
    // Free result sets
    $result->free();
    $districtsResult->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

<?php
session_start(); // Ensure the session is started
$title = "Edit Tehsil";
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

// Fetch the tehsil details if an ID is provided
if (isset($_GET['id'])) {
    $tehsilId = intval($_GET['id']);
    
    // Fetch tehsil details
    $stmt = $conn->prepare("SELECT TehsilName, DistrictID FROM Tehsils WHERE TehsilID = ?");
    $stmt->bind_param("i", $tehsilId);
    $stmt->execute();
    $stmt->bind_result($tehsilName, $districtId);
    $stmt->fetch();
    $stmt->close();

    if (!$tehsilName) {
        header("Location: manage_tehsils.php"); // Redirect if no tehsil found
        exit();
    }
}

// Handle the update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_tehsil'])) {
    $tehsilName = trim($_POST['tehsil_name']);
    $districtId = intval($_POST['district_id']);

    if (!empty($tehsilName) && $districtId > 0) {
        $stmt = $conn->prepare("UPDATE Tehsils SET TehsilName = ?, DistrictID = ? WHERE TehsilID = ?");
        $stmt->bind_param("sii", $tehsilName, $districtId, $tehsilId);
        
        if ($stmt->execute()) {
            $message = "Tehsil updated successfully.";
            header("Location: manage_tehsils.php"); // Redirect after successful update
            exit();
        } else {
            $message = "Error updating tehsil: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a tehsil name and select a district.";
    }
}

// Fetch all districts for dropdown
$districtsQuery = "SELECT DistrictID, DistrictName FROM Districts";
$districtsResult = $conn->query($districtsQuery);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Edit Tehsil</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Edit Tehsil Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $tehsilId; ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="tehsil_name" class="form-label">Tehsil Name<span class="text-danger">*</span></label>
            <input type="text" name="tehsil_name" class="form-control" id="tehsil_name" value="<?= htmlspecialchars($tehsilName) ?>" required>
        </div>
        <div class="mb-3">
            <label for="district_id" class="form-label">Select District<span class="text-danger">*</span></label>
            <select name="district_id" class="form-control" id="district_id" required>
                <option value="">Select District</option>
                <?php while ($district = $districtsResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($district['DistrictID']) ?>" <?= $district['DistrictID'] == $districtId ? 'selected' : '' ?>><?= htmlspecialchars($district['DistrictName']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="edit_tehsil" class="btn btn-primary">Update Tehsil</button>
    </form>

    <?php
    // Free result set
    $districtsResult->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

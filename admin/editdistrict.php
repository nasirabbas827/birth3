<?php
session_start(); // Ensure the session is started
$title = "Edit District";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}


$districtId = $_GET['id'];
$message = "";

// Fetch district for editing
$stmt = $conn->prepare("SELECT DistrictName FROM Districts WHERE DistrictID = ?");
$stmt->bind_param("i", $districtId);
$stmt->execute();
$stmt->bind_result($districtName);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newDistrictName = trim($_POST['district_name']);
    
    if (!empty($newDistrictName)) {
        // Update district name
        $stmt = $conn->prepare("UPDATE Districts SET DistrictName = ? WHERE DistrictID = ?");
        $stmt->bind_param("si", $newDistrictName, $districtId);
        
        if ($stmt->execute()) {
            $message = "District updated successfully.";
            header("Location: managedistricts.php");
            exit;
        } else {
            $message = "Error updating district: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a district name.";
    }
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Edit District</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Edit District Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . "?id=" . htmlspecialchars($districtId); ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="district_name" class="form-label">District Name<span class="text-danger">*</span></label>
            <input type="text" name="district_name" class="form-control" id="district_name" value="<?= htmlspecialchars($districtName) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update District</button>
    </form>
</main>

<?php include('include/footer.inc'); ?>

<?php
session_start(); // Ensure the session is started
$title = "Edit Fee";
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

// Fetch the fee details if an ID is provided
if (isset($_GET['id'])) {
    $feeId = intval($_GET['id']);
    
    // Fetch fee details
    $stmt = $conn->prepare("SELECT fee_type, fee FROM Fees WHERE fee_id = ?");
    $stmt->bind_param("i", $feeId);
    $stmt->execute();
    $stmt->bind_result($feeType, $fee);
    $stmt->fetch();
    $stmt->close();

    if (!$feeType) {
        header("Location: manage_fees.php"); // Redirect if no fee found
        exit();
    }
}

// Handle the update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_fee'])) {
    $feeType = $_POST['fee_type'];
    $fee = floatval($_POST['fee']);

    if (!empty($feeType) && $fee >= 0) {
        $stmt = $conn->prepare("UPDATE Fees SET fee_type = ?, fee = ? WHERE fee_id = ?");
        $stmt->bind_param("sdi", $feeType, $fee, $feeId);
        
        if ($stmt->execute()) {
            $message = "Fee updated successfully.";
            header("Location: manage_fees.php"); // Redirect after successful update
            exit();
        } else {
            $message = "Error updating fee: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a valid fee type and amount.";
    }
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Edit Fee</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Edit Fee Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $feeId; ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="fee_type" class="form-label">Fee Type<span class="text-danger">*</span></label>
            <select name="fee_type" class="form-control" id="fee_type" required>
                <option value="">Select Fee Type</option>
                <option value="Birth Certificate" <?= $feeType == "Birth Certificate" ? 'selected' : '' ?>>Birth Certificate</option>
                <option value="Death Certificate" <?= $feeType == "Death Certificate" ? 'selected' : '' ?>>Death Certificate</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="fee" class="form-label">Fee Amount<span class="text-danger">*</span></label>
            <input type="number" name="fee" class="form-control" id="fee" value="<?= htmlspecialchars($fee) ?>" step="0.01" min="0" required>
        </div>
        <button type="submit" name="edit_fee" class="btn btn-primary">Update Fee</button>
    </form>
</main>

<?php include('include/footer.inc'); ?>

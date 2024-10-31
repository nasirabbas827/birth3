<?php
session_start(); // Ensure the session is started
$title = "Manage Fees";
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

// Handle fee addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_fee'])) {
    $feeType = $_POST['fee_type'];
    $fee = floatval($_POST['fee']);

    if (!empty($feeType) && $fee >= 0) {
        // Insert new fee
        $stmt = $conn->prepare("INSERT INTO Fees (fee_type, fee) VALUES (?, ?)");
        $stmt->bind_param("sd", $feeType, $fee);
        
        if ($stmt->execute()) {
            $message = "Fee added successfully.";
        } else {
            $message = "Error adding fee: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a valid fee type and amount.";
    }
}

// Handle fee deletion
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM Fees WHERE fee_id = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        $message = "Fee deleted successfully.";
    } else {
        $message = "Error deleting fee: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all fees
$query = "SELECT fee_id, fee_type, fee FROM Fees";
$result = $conn->query($query);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Manage Fees</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Fee Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="fee_type" class="form-label">Fee Type<span class="text-danger">*</span></label>
            <select name="fee_type" class="form-control" id="fee_type" required>
                <option value="">Select Fee Type</option>
                <option value="Birth Certificate">Birth Certificate</option>
                <option value="Death Certificate">Death Certificate</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="fee" class="form-label">Fee Amount<span class="text-danger">*</span></label>
            <input type="number" name="fee" class="form-control" id="fee" step="0.01" min="0" required>
        </div>
        <button type="submit" name="add_fee" class="btn btn-primary">Add Fee</button>
    </form>

    <h2 class="text-center">Existing Fees</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fee ID</th>
                    <th>Fee Type</th>
                    <th>Fee Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fee = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fee['fee_id']) ?></td>
                        <td><?= htmlspecialchars($fee['fee_type']) ?></td>
                        <td><?= htmlspecialchars($fee['fee']) ?></td>
                        <td>
                            <a href="editfee.php?id=<?= $fee['fee_id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?delete_id=<?= $fee['fee_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this fee?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h3>No fees found.</h3>
        </div>
    <?php endif; ?>

    <?php
    // Free result set
    $result->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

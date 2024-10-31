<?php
session_start(); // Ensure the session is started
$title = "Edit Union Council";
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

// Fetch the union council details if an ID is provided
if (isset($_GET['id'])) {
    $unionCouncilId = intval($_GET['id']);
    
    // Fetch union council details
    $stmt = $conn->prepare("SELECT UnionCouncilName, TehsilID FROM UnionCouncils WHERE UnionCouncilID = ?");
    $stmt->bind_param("i", $unionCouncilId);
    $stmt->execute();
    $stmt->bind_result($unionCouncilName, $tehsilId);
    $stmt->fetch();
    $stmt->close();

    if (!$unionCouncilName) {
        header("Location: manage_union_councils.php"); // Redirect if no union council found
        exit();
    }
}

// Handle the update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_union_council'])) {
    $unionCouncilName = trim($_POST['union_council_name']);
    $tehsilId = intval($_POST['tehsil_id']);

    if (!empty($unionCouncilName) && $tehsilId > 0) {
        $stmt = $conn->prepare("UPDATE UnionCouncils SET UnionCouncilName = ?, TehsilID = ? WHERE UnionCouncilID = ?");
        $stmt->bind_param("sii", $unionCouncilName, $tehsilId, $unionCouncilId);
        
        if ($stmt->execute()) {
            $message = "Union council updated successfully.";
            header("Location: manage_union_councils.php"); // Redirect after successful update
            exit();
        } else {
            $message = "Error updating union council: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a union council name and select a tehsil.";
    }
}

// Fetch all tehsils for dropdown
$tehsilsQuery = "SELECT TehsilID, TehsilName FROM Tehsils";
$tehsilsResult = $conn->query($tehsilsQuery);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Edit Union Council</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Edit Union Council Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $unionCouncilId; ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="union_council_name" class="form-label">Union Council Name<span class="text-danger">*</span></label>
            <input type="text" name="union_council_name" class="form-control" id="union_council_name" value="<?= htmlspecialchars($unionCouncilName) ?>" required>
        </div>
        <div class="mb-3">
            <label for="tehsil_id" class="form-label">Select Tehsil<span class="text-danger">*</span></label>
            <select name="tehsil_id" class="form-control" id="tehsil_id" required>
                <option value="">Select Tehsil</option>
                <?php while ($tehsil = $tehsilsResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($tehsil['TehsilID']) ?>" <?= $tehsil['TehsilID'] == $tehsilId ? 'selected' : '' ?>><?= htmlspecialchars($tehsil['TehsilName']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="edit_union_council" class="btn btn-primary">Update Union Council</button>
    </form>

    <?php
    // Free result set
    $tehsilsResult->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

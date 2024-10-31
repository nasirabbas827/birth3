<?php
session_start(); // Ensure the session is started
$title = "Manage Union Councils";
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

// Handle union council addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_union_council'])) {
    $unionCouncilName = trim($_POST['union_council_name']);
    $tehsilId = intval($_POST['tehsil_id']);
    
    if (!empty($unionCouncilName) && $tehsilId > 0) {
        // Insert new union council
        $stmt = $conn->prepare("INSERT INTO UnionCouncils (UnionCouncilName, TehsilID) VALUES (?, ?)");
        $stmt->bind_param("si", $unionCouncilName, $tehsilId);
        
        if ($stmt->execute()) {
            $message = "Union council added successfully.";
        } else {
            $message = "Error adding union council: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please provide a union council name and select a tehsil.";
    }
}

// Handle union council deletion
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM UnionCouncils WHERE UnionCouncilID = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        $message = "Union council deleted successfully.";
    } else {
        $message = "Error deleting union council: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all union councils
$query = "SELECT uc.UnionCouncilID, uc.UnionCouncilName, t.TehsilName 
          FROM UnionCouncils uc
          JOIN Tehsils t ON uc.TehsilID = t.TehsilID";
$result = $conn->query($query);

// Fetch all tehsils for dropdown
$tehsilsQuery = "SELECT TehsilID, TehsilName FROM Tehsils";
$tehsilsResult = $conn->query($tehsilsQuery);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Manage Union Councils</h1>

    <!-- Display message -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Union Council Form -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="union_council_name" class="form-label">Union Council Name<span class="text-danger">*</span></label>
            <input type="text" name="union_council_name" class="form-control" id="union_council_name" required>
        </div>
        <div class="mb-3">
            <label for="tehsil_id" class="form-label">Select Tehsil<span class="text-danger">*</span></label>
            <select name="tehsil_id" class="form-control" id="tehsil_id" required>
                <option value="">Select Tehsil</option>
                <?php while ($tehsil = $tehsilsResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($tehsil['TehsilID']) ?>"><?= htmlspecialchars($tehsil['TehsilName']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="add_union_council" class="btn btn-primary">Add Union Council</button>
    </form>

    <h2 class="text-center">Existing Union Councils</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Union Council ID</th>
                    <th>Union Council Name</th>
                    <th>Tehsil Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($unionCouncil = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($unionCouncil['UnionCouncilID']) ?></td>
                        <td><?= htmlspecialchars($unionCouncil['UnionCouncilName']) ?></td>
                        <td><?= htmlspecialchars($unionCouncil['TehsilName']) ?></td>
                        <td>
                            <a href="editunioncouncil.php?id=<?= $unionCouncil['UnionCouncilID'] ?>" class="btn btn-warning">Edit</a>
                            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?delete_id=<?= $unionCouncil['UnionCouncilID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this union council?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h3>No union councils found.</h3>
        </div>
    <?php endif; ?>

    <?php
    // Free result sets
    $result->free();
    $tehsilsResult->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

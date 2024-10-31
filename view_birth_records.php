<?php
session_start(); // Ensure the session is started
$title = "View Birth Records";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id'];
$message = "";

// Fetch birth records for the logged-in user
$recordsQuery = "SELECT * FROM BirthRecords WHERE UserID = ?";
$stmt = $conn->prepare($recordsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$recordsResult = $stmt->get_result();
$records = $recordsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle deletion of a record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_record'])) {
    $recordId = $_POST['record_id'];
    
    // Check if the record can be deleted
    $checkQuery = "SELECT PaymentStatus FROM BirthRecords WHERE BirthRecordID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $recordId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $record = $checkResult->fetch_assoc();
        if ($record['PaymentStatus'] == 'Paid') {
            $message = "Cannot delete records with 'Paid' status.";
        } else {
            // Proceed to delete
            $deleteQuery = "DELETE FROM BirthRecords WHERE BirthRecordID = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $recordId);
            if ($deleteStmt->execute()) {
                $message = "Birth record deleted successfully.";
            } else {
                $message = "Error deleting record: " . $deleteStmt->error;
            }
            $deleteStmt->close();
        }
    } else {
        $message = "Record not found.";
    }
    $checkStmt->close();
}

// Function to generate PDF certificate
function generateCertificate($recordId) {
    header("Location: print_certificate.php?record_id=" . $recordId);
    exit();
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Your Birth Records</h1>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Child Name</th>
                <th>Birth Date</th>
                <th>Gender</th>
                <th>Mother NIC</th>
                <th>Father NIC</th>
                <th>Birth Place</th>
                <th>Payment Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($records) > 0): ?>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['ChildName']) ?></td>
                        <td><?= htmlspecialchars($record['BirthDate']) ?></td>
                        <td><?= htmlspecialchars($record['Gender']) ?></td>
                        <td><?= htmlspecialchars($record['MotherNIC']) ?></td>
                        <td><?= htmlspecialchars($record['FatherNIC']) ?></td>
                        <td><?= htmlspecialchars($record['BirthPlace']) ?></td>
                        <td><?= htmlspecialchars($record['PaymentStatus']) ?></td>
                        <td>
                            <?php if ($record['PaymentStatus'] == 'Paid'): ?>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="record_id" value="<?= $record['BirthRecordID'] ?>">
                                    <button type="button" class="btn btn-success" onclick="generateCertificate(<?= $record['BirthRecordID'] ?>)">Print Certificate</button>
                                </form>
                            <?php else: ?>
                                <a href="edit_birth_record.php?id=<?= $record['BirthRecordID'] ?>" class="btn btn-warning">Edit</a>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="record_id" value="<?= $record['BirthRecordID'] ?>">
                                    <button type="submit" name="delete_record" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No birth records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include('include/footer.inc'); ?>

<script>
    function generateCertificate(recordId) {
        window.location.href = "print_birth_certificate.php?record_id=" + recordId;
    }
</script>

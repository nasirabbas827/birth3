<?php
session_start(); // Ensure the session is started
$title = "View Death Records";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id'];
$records = [];

// Fetch the user's death records
$query = "SELECT * FROM DeathRecords WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}

$stmt->close();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $deleteQuery = "DELETE FROM DeathRecords WHERE DeathRecordID = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $deleteId);
    if ($deleteStmt->execute()) {
        $deleteMessage = "Record deleted successfully.";
        header("Location: view_death_records.php");
        exit;
    } else {
        $deleteMessage = "Error deleting record. Please try again.";
    }
    $deleteStmt->close();
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Your Death Records</h1>

    <?php if (isset($deleteMessage)): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($deleteMessage) ?></div>
    <?php endif; ?>

    <?php if (count($records) === 0): ?>
        <div class="alert alert-info text-center">No death records found.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Deceased Name</th>
                    <th>Father Name</th>
                    <th>Death Date</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['DeceasedName']) ?></td>
                        <td><?= htmlspecialchars($record['FatherName']) ?></td>
                        <td><?= htmlspecialchars($record['DeathDate']) ?></td>
                        <td><?= htmlspecialchars($record['PaymentStatus']) ?></td>
                        <td>
                            <?php if ($record['PaymentStatus'] === 'Unpaid'): ?>
                                <a href="edit_death_record.php?id=<?= $record['DeathRecordID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $record['DeathRecordID'] ?>)">Delete</button>
                            <?php else: ?>
                                <a href="print_death_certificate.php?id=<?= $record['DeathRecordID'] ?>" class="btn btn-success">Print Certificate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<script>
function confirmDelete(recordId) {
    if (confirm("Are you sure you want to delete this record?")) {
        // Create a form dynamically to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input type="hidden" name="delete_id" value="${recordId}">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
include('include/footer.inc'); 
$conn->close(); // Close database connection
?>

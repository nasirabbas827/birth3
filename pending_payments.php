<?php
session_start();
$title = "Pending Payments";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch pending payments from death records and birth records for the logged-in user
$userId = $_SESSION['id'];
$pendingPayments = [];

// Query for pending payments in death records
$deathQuery = "SELECT DeathRecordID, DeceasedName, Fee FROM deathrecords WHERE PaymentStatus = 'Unpaid' AND UserID = ?";
$stmt = $conn->prepare($deathQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$deathResult = $stmt->get_result();
while ($row = $deathResult->fetch_assoc()) {
    $pendingPayments[] = [
        'type' => 'Death Record',
        'id' => $row['DeathRecordID'],
        'name' => $row['DeceasedName'],
        'fee' => $row['Fee']
    ];
}
$stmt->close();

// Query for pending payments in birth records
$birthQuery = "SELECT BirthRecordID, ChildName, Fee FROM birthrecords WHERE PaymentStatus = 'Unpaid' AND UserID = ?";
$stmt = $conn->prepare($birthQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$birthResult = $stmt->get_result();
while ($row = $birthResult->fetch_assoc()) {
    $pendingPayments[] = [
        'type' => 'Birth Record',
        'id' => $row['BirthRecordID'],
        'name' => $row['ChildName'],
        'fee' => $row['Fee']
    ];
}
$stmt->close();
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Pending Payments</h1>

    <?php if (empty($pendingPayments)): ?>
        <div class="alert alert-info text-center">No pending payments found.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Fee</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingPayments as $payment): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['type']) ?></td>
                        <td><?= htmlspecialchars($payment['name']) ?></td>
                        <td><?= htmlspecialchars($payment['fee']) ?></td>
                        <td>
                            <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="record_id" value="<?= htmlspecialchars($payment['id']) ?>">
                                <input type="hidden" name="record_type" value="<?= htmlspecialchars($payment['type']) ?>">
                                <div class="mb-3">
                                    <label for="transaction_image" class="form-label">Upload Transaction Image</label>
                                    <input type="file" name="transaction_image" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Pay Now</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php include('include/footer.inc'); ?>

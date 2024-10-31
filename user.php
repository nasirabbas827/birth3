<?php
session_start(); // Ensure the session is started
$title = "User Dashboard";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's fees from the database
$userId = $_SESSION['id'];
$query = "SELECT fee_id, fee_type, fee FROM fees"; // Adjust this query if necessary
$result = $conn->query($query);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>

    <div class="text-center mb-4">
        <a href="birth_records.php" class="btn btn-primary">Birth Records</a>
        <a href="death_records.php" class="btn btn-danger">Death Records</a>
        <a href="pending_payments.php" class="btn btn-warning">Pending Payments</a>
    </div>

    <h2 class="text-center">Fees Overview</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fee ID</th>
                    <th>Fee Type</th>
                    <th>Fee Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fee = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($fee['fee_id']) ?></td>
                        <td><?= htmlspecialchars($fee['fee_type']) ?></td>
                        <td><?= htmlspecialchars($fee['fee']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h3>No fees available.</h3>
        </div>
    <?php endif; ?>

    <?php
    // Free result set
    $result->free();
    ?>
</main>

<?php include('include/footer.inc'); ?>

<?php
session_start(); // Ensure the session is started
$title = "Admin Dashboard";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch counts for birth records, death records, tehsils, union councils, districts, and users
$totalBirthRecordsQuery = "SELECT COUNT(*) as total FROM birthrecords";
$totalDeathRecordsQuery = "SELECT COUNT(*) as total FROM deathrecords";
$totalTehsilsQuery = "SELECT COUNT(*) as total FROM tehsils";
$totalUnionCouncilsQuery = "SELECT COUNT(*) as total FROM unioncouncils";
$totalDistrictsQuery = "SELECT COUNT(*) as total FROM districts";
$totalUsersQuery = "SELECT COUNT(*) as total FROM users";

$totalBirthRecords = $conn->query($totalBirthRecordsQuery)->fetch_assoc()['total'];
$totalDeathRecords = $conn->query($totalDeathRecordsQuery)->fetch_assoc()['total'];
$totalTehsils = $conn->query($totalTehsilsQuery)->fetch_assoc()['total'];
$totalUnionCouncils = $conn->query($totalUnionCouncilsQuery)->fetch_assoc()['total'];
$totalDistricts = $conn->query($totalDistrictsQuery)->fetch_assoc()['total'];
$totalUsers = $conn->query($totalUsersQuery)->fetch_assoc()['total'];
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Admin Dashboard</h1>
    
    <!-- Display Total Counts -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Birth Records</h5>
                    <p class="card-text"><?= htmlspecialchars($totalBirthRecords) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Death Records</h5>
                    <p class="card-text"><?= htmlspecialchars($totalDeathRecords) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Tehsils</h5>
                    <p class="card-text"><?= htmlspecialchars($totalTehsils) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">UC's</h5>
                    <p class="card-text"><?= htmlspecialchars($totalUnionCouncils) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Districts</h5>
                    <p class="card-text"><?= htmlspecialchars($totalDistricts) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text"><?= htmlspecialchars($totalUsers) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fetch and Display Birth Records -->
    <h2 class="m-4 text-center">Birth Records</h2>
    <?php
    // Fetch birth records from the database
    $query = "SELECT br.BirthRecordID, br.ChildName, br.BirthDate, br.Gender, br.BirthPlace, 
              br.PaymentStatus, br.Fee, uc.UnionCouncilName, d.DistrictName, t.TehsilName, 
              br.TransactionImage 
              FROM birthrecords br 
              LEFT JOIN unioncouncils uc ON br.UnionCouncilID = uc.UnionCouncilID 
              LEFT JOIN districts d ON br.DistrictID = d.DistrictID 
              LEFT JOIN tehsils t ON br.TehsilID = t.TehsilID";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0): ?>
        <?php while ($record = $result->fetch_assoc()): ?>
            <div class="row mb-5">
                <div class="col-md-4">
                    <img src="../<?= htmlspecialchars($record['TransactionImage']) ?>" alt="Transaction Image" class="img-fluid mb-3">
                </div>
                <div class="col-md-8">
                    <h2><?= htmlspecialchars($record['ChildName']) ?></h2>
                    <p><strong>Birth Date:</strong> <?= htmlspecialchars($record['BirthDate']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($record['Gender']) ?></p>
                    <p><strong>Birth Place:</strong> <?= htmlspecialchars($record['BirthPlace']) ?></p>
                    <p><strong>Payment Status:</strong> <?= htmlspecialchars($record['PaymentStatus']) ?></p>
                    <p><strong>Fee:</strong> <?= htmlspecialchars($record['Fee']) ?></p>
                    <p><strong>Union Council:</strong> <?= htmlspecialchars($record['UnionCouncilName']) ?></p>
                    <p><strong>District:</strong> <?= htmlspecialchars($record['DistrictName']) ?></p>
                    <p><strong>Tehsil:</strong> <?= htmlspecialchars($record['TehsilName']) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <h3>No birth records found.</h3>
            <p class="mb-4">Click the button below to add a new record!</p>
        </div>
    <?php endif; ?>

    <?php
    // Free result set and close statement
    $result->free();
    $stmt->close();
    ?>
</main>

<?php include('include/footer.inc'); ?>

<?php
session_start();
$title = "Edit Death Record";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$recordId = $_GET['id'] ?? null;

// Fetch the death record details
if ($recordId) {
    $recordQuery = "SELECT * FROM deathrecords WHERE DeathRecordID = ?";
    $stmt = $conn->prepare($recordQuery);
    $stmt->bind_param("i", $recordId);
    $stmt->execute();
    $recordResult = $stmt->get_result();
    $record = $recordResult->fetch_assoc();
    $stmt->close();

    // Fetch names for Union Council, District, and Tehsil
    $unionCouncilQuery = "SELECT UnionCouncilName FROM unioncouncils WHERE UnionCouncilID = ?";
    $districtQuery = "SELECT DistrictName FROM districts WHERE DistrictID = ?";
    $tehsilQuery = "SELECT TehsilName FROM tehsils WHERE TehsilID = ?";

    // Union Council
    $stmt = $conn->prepare($unionCouncilQuery);
    $stmt->bind_param("s", $record['UnionCouncilID']);
    $stmt->execute();
    $unionResult = $stmt->get_result();
    $unionCouncilName = $unionResult->fetch_assoc()['UnionCouncilName'];
    $stmt->close();

    // District
    $stmt = $conn->prepare($districtQuery);
    $stmt->bind_param("s", $record['DistrictID']);
    $stmt->execute();
    $districtResult = $stmt->get_result();
    $districtName = $districtResult->fetch_assoc()['DistrictName'];
    $stmt->close();

    // Tehsil
    $stmt = $conn->prepare($tehsilQuery);
    $stmt->bind_param("s", $record['TehsilID']);
    $stmt->execute();
    $tehsilResult = $stmt->get_result();
    $tehsilName = $tehsilResult->fetch_assoc()['TehsilName'];
    $stmt->close();

    // Handle record update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $deceasedName = $_POST['deceased_name'];
        $fatherName = $_POST['father_name'];
        $fatherNIC = $_POST['father_nic'];
        $deathDate = $_POST['death_date'];
        $causeOfDeath = $_POST['cause_of_death'];
        $nicNumber = $_POST['nic_number'];
        $deathPlace = $_POST['death_place'];

        $updateQuery = "UPDATE deathrecords SET DeceasedName = ?, FatherName = ?, FatherNIC = ?, DeathDate = ?, CauseOfDeath = ?, NICNumber = ?, DeathPlace = ? WHERE DeathRecordID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssssssi", $deceasedName, $fatherName, $fatherNIC, $deathDate, $causeOfDeath, $nicNumber, $deathPlace, $recordId);
        
        if ($updateStmt->execute()) {
            $message = "Death record updated successfully.";
        } else {
            $message = "Error updating record: " . $updateStmt->error;
        }
        $updateStmt->close();
    }
} else {
    header("Location: view_death_records.php"); // Redirect if no record ID is provided
    exit();
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Edit Death Record</h1>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6 mb-3">
            <label for="deceased_name" class="form-label">Deceased Name</label>
            <input type="text" class="form-control" name="deceased_name" value="<?= htmlspecialchars($record['DeceasedName']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="father_name" class="form-label">Father Name</label>
            <input type="text" class="form-control" name="father_name" value="<?= htmlspecialchars($record['FatherName']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="father_nic" class="form-label">Father NIC</label>
            <input type="text" class="form-control" name="father_nic" value="<?= htmlspecialchars($record['FatherNIC']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="death_date" class="form-label">Death Date</label>
            <input type="date" class="form-control" name="death_date" value="<?= htmlspecialchars($record['DeathDate']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="cause_of_death" class="form-label">Cause of Death</label>
            <input type="text" class="form-control" name="cause_of_death" value="<?= htmlspecialchars($record['CauseOfDeath']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="nic_number" class="form-label">NIC Number</label>
            <input type="text" class="form-control" name="nic_number" value="<?= htmlspecialchars($record['NICNumber']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="payment_status" class="form-label">Payment Status</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($record['PaymentStatus']) ?>" readonly>
        </div>
        <div class="col-md-6 mb-3">
            <label for="death_place" class="form-label">Death Place</label>
            <input type="text" class="form-control" name="death_place" value="<?= htmlspecialchars($record['DeathPlace']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="fee" class="form-label">Fee</label>
            <input type="number" class="form-control" value="<?= htmlspecialchars($record['Fee']) ?>" readonly>
        </div>
        <div class="col-md-6 mb-3">
            <label for="union_council" class="form-label">Union Council</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($unionCouncilName) ?>" readonly>
        </div>
        <div class="col-md-6 mb-3">
            <label for="district" class="form-label">District</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($districtName) ?>" readonly>
        </div>
        <div class="col-md-6 mb-3">
            <label for="tehsil" class="form-label">Tehsil</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($tehsilName) ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Update Record</button>
    </form>
</main>

<?php include('include/footer.inc'); ?>

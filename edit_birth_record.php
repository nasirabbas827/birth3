<?php
session_start(); // Ensure the session is started
$title = "Edit Birth Record";
include('include/db_connect.inc'); // Database connection
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Get the record ID from the URL
if (!isset($_GET['id'])) {
    header("Location: view_birth_records.php"); // Redirect if no ID is provided
    exit();
}

$recordId = $_GET['id'];
$userId = $_SESSION['id'];

// Fetch the birth record data
$query = "SELECT * FROM BirthRecords WHERE BirthRecordID = ? AND UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $recordId, $userId);
$stmt->execute();
$recordResult = $stmt->get_result();

if ($recordResult->num_rows === 0) {
    header("Location: view_birth_records.php"); // Redirect if record not found or does not belong to user
    exit();
}

$record = $recordResult->fetch_assoc();
$stmt->close();

// Handle form submission to update record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $childName = $_POST['child_name'];
    $birthDate = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $motherNIC = $_POST['mother_nic'];
    $fatherNIC = $_POST['father_nic'];
    $birthPlace = $_POST['birth_place'];
    $tehsilID = $_POST['tehsil_id'];
    $districtID = $_POST['district_id'];
    $unionCouncilID = $_POST['union_council_id'];

    // Validate NIC lengths
    if (strlen($motherNIC) !== 13 || strlen($fatherNIC) !== 13) {
        $error = "NIC numbers must be exactly 13 digits.";
    } else {
        // Update the record
        $updateQuery = "UPDATE BirthRecords SET ChildName = ?, BirthDate = ?, Gender = ?, MotherNIC = ?, FatherNIC = ?, BirthPlace = ?, TehsilID = ?, DistrictID = ?, UnionCouncilID = ? WHERE BirthRecordID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssssssss", $childName, $birthDate, $gender, $motherNIC, $fatherNIC, $birthPlace, $tehsilID, $districtID, $unionCouncilID, $recordId);

        if ($updateStmt->execute()) {
            header("Location: view_birth_records.php"); // Redirect to view page on success
            exit();
        } else {
            $error = "Error updating record: " . $updateStmt->error;
        }
        $updateStmt->close();
    }
}

// Fetch districts, tehsils, and union councils for the dropdowns
$districtsQuery = "SELECT DistrictID, DistrictName FROM Districts";
$districtsResult = $conn->query($districtsQuery);

$tehsilsQuery = "SELECT TehsilID, TehsilName FROM Tehsils";
$tehsilsResult = $conn->query($tehsilsQuery);

$unionCouncilsQuery = "SELECT UnionCouncilID, UnionCouncilName FROM UnionCouncils";
$unionCouncilsResult = $conn->query($unionCouncilsQuery);
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Edit Birth Record</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="child_name" class="form-label">Child Name</label>
                <input type="text" class="form-control" id="child_name" name="child_name" value="<?= htmlspecialchars($record['ChildName']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?= htmlspecialchars($record['BirthDate']) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="Male" <?= $record['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $record['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="mother_nic" class="form-label">Mother NIC Number</label>
                <input type="text" class="form-control" id="mother_nic" name="mother_nic" value="<?= htmlspecialchars($record['MotherNIC']) ?>" maxlength="13" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="father_nic" class="form-label">Father NIC Number</label>
                <input type="text" class="form-control" id="father_nic" name="father_nic" value="<?= htmlspecialchars($record['FatherNIC']) ?>" maxlength="13" required>
            </div>
            <div class="col-md-6">
                <label for="birth_place" class="form-label">Birth Place</label>
                <input type="text" class="form-control" id="birth_place" name="birth_place" value="<?= htmlspecialchars($record['BirthPlace']) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="tehsil_id" class="form-label">Tehsil</label>
                <select class="form-select" id="tehsil_id" name="tehsil_id" required>
                    <?php while ($tehsil = $tehsilsResult->fetch_assoc()): ?>
                        <option value="<?= $tehsil['TehsilID'] ?>" <?= $tehsil['TehsilID'] == $record['TehsilID'] ? 'selected' : '' ?>><?= htmlspecialchars($tehsil['TehsilName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="district_id" class="form-label">District</label>
                <select class="form-select" id="district_id" name="district_id" required>
                    <?php while ($district = $districtsResult->fetch_assoc()): ?>
                        <option value="<?= $district['DistrictID'] ?>" <?= $district['DistrictID'] == $record['DistrictID'] ? 'selected' : '' ?>><?= htmlspecialchars($district['DistrictName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="union_council_id" class="form-label">Union Council</label>
                <select class="form-select" id="union_council_id" name="union_council_id" required>
                    <?php while ($unionCouncil = $unionCouncilsResult->fetch_assoc()): ?>
                        <option value="<?= $unionCouncil['UnionCouncilID'] ?>" <?= $unionCouncil['UnionCouncilID'] == $record['UnionCouncilID'] ? 'selected' : '' ?>><?= htmlspecialchars($unionCouncil['UnionCouncilName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="payment_status" class="form-label">Payment Status</label>
                <input type="text" class="form-control" id="payment_status" name="payment_status" value="<?= htmlspecialchars($record['PaymentStatus']) ?>" disabled>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Record</button>
        <a href="view_birth_records.php" class="btn btn-secondary">Cancel</a>
    </form>
</main>

<?php include('include/footer.inc'); ?>

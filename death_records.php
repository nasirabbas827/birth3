<?php
session_start(); // Ensure the session is started
$title = "Add Death Record";
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

// Fetch available fees for death certificate
$feeQuery = "SELECT fee FROM fees WHERE fee_type = 'Death Certificate'";
$feeResult = $conn->query($feeQuery);
$feeAmount = $feeResult->fetch_assoc()['fee'] ?? 0;

// Fetch districts
$districts = [];
$districtQuery = "SELECT DistrictID, DistrictName FROM Districts";
$districtResult = $conn->query($districtQuery);
if ($districtResult) {
    while ($district = $districtResult->fetch_assoc()) {
        $districts[$district['DistrictID']] = $district['DistrictName'];
    }
}

// Fetch tehsils
$tehsils = [];
$tehsilQuery = "SELECT TehsilID, TehsilName FROM Tehsils";
$tehsilResult = $conn->query($tehsilQuery);
if ($tehsilResult) {
    while ($tehsil = $tehsilResult->fetch_assoc()) {
        $tehsils[$tehsil['TehsilID']] = $tehsil['TehsilName'];
    }
}

// Fetch union councils
$unionCouncils = [];
$unionCouncilQuery = "SELECT UnionCouncilID, UnionCouncilName FROM UnionCouncils";
$unionCouncilResult = $conn->query($unionCouncilQuery);
if ($unionCouncilResult) {
    while ($unionCouncil = $unionCouncilResult->fetch_assoc()) {
        $unionCouncils[$unionCouncil['UnionCouncilID']] = $unionCouncil['UnionCouncilName'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_record'])) {
    $deceasedName = trim($_POST['deceased_name']);
    $fatherName = trim($_POST['father_name']);
    $fatherNIC = trim($_POST['father_nic']);
    $deathDate = $_POST['death_date'];
    $causeOfDeath = trim($_POST['cause_of_death']);
    $nicNumber = trim($_POST['nic_number']);
    $deathPlace = trim($_POST['death_place']);
    $districtId = $_POST['district_id'];
    $tehsilId = $_POST['tehsil_id'];
    $unionCouncilId = $_POST['union_council_id'];
    $userId = $_SESSION['id']; // Fetching the logged-in user's ID
    $PaymentStatus = 'Unpaid';

    // Validate NIC numbers
    if (strlen($fatherNIC) !== 13 || strlen($nicNumber) !== 13) {
        $message = "NIC numbers must be 13 digits.";
    } else {
        // Insert death record
        $stmt = $conn->prepare("INSERT INTO deathrecords (DeceasedName, FatherName, FatherNIC, DeathDate, CauseOfDeath, NICNumber, PaymentStatus, DeathPlace, Fee, UnionCouncilID, DistrictID, TehsilID, UserID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
        
        // Use correct data types for bind_param
        $stmt->bind_param("sssssssssssss", $deceasedName, $fatherName, $fatherNIC, $deathDate, $causeOfDeath, $nicNumber, $PaymentStatus, $deathPlace, $feeAmount, $unionCouncilId, $districtId, $tehsilId, $userId);
        
        // Execute statement and handle errors
        try {
            if ($stmt->execute()) {
                $message = "Death record added successfully.";
            } else {
                $message = "Error adding record: " . $stmt->error;
            }
        } catch (mysqli_sql_exception $e) {
            $message = "Database error: " . $e->getMessage();
        }
        
        $stmt->close();
    }
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Add Death Record</h1>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="deceased_name" class="form-label">Deceased Name<span class="text-danger">*</span></label>
                <input type="text" name="deceased_name" class="form-control" id="deceased_name" required>
            </div>
            <div class="col-md-6">
                <label for="father_name" class="form-label">Father Name<span class="text-danger">*</span></label>
                <input type="text" name="father_name" class="form-control" id="father_name" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="father_nic" class="form-label">Father NIC Number<span class="text-danger">*</span></label>
                <input type="text" name="father_nic" class="form-control" id="father_nic" required maxlength="13" minlength="13" pattern="\d{13}">
            </div>
            <div class="col-md-6">
                <label for="nic_number" class="form-label">Deceased NIC Number<span class="text-danger">*</span></label>
                <input type="text" name="nic_number" class="form-control" id="nic_number" required maxlength="13" minlength="13" pattern="\d{13}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="death_date" class="form-label">Death Date<span class="text-danger">*</span></label>
                <input type="date" name="death_date" class="form-control" id="death_date" required>
            </div>
            <div class="col-md-6">
                <label for="cause_of_death" class="form-label">Cause of Death<span class="text-danger">*</span></label>
                <input type="text" name="cause_of_death" class="form-control" id="cause_of_death" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="fee" class="form-label">Fee for Death Certificate</label>
                <input type="text" class="form-control" id="fee" value="<?= htmlspecialchars($feeAmount) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label for="death_place" class="form-label">Death Place<span class="text-danger">*</span></label>
                <input type="text" name="death_place" class="form-control" id="death_place" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="district_id" class="form-label">District<span class="text-danger">*</span></label>
                <select name="district_id" class="form-select" id="district_id" required>
                    <option value="" disabled selected>Select District</option>
                    <?php foreach ($districts as $districtId => $districtName): ?>
                        <option value="<?= $districtId ?>"><?= htmlspecialchars($districtName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tehsil_id" class="form-label">Tehsil<span class="text-danger">*</span></label>
                <select name="tehsil_id" class="form-select" id="tehsil_id" required>
                    <option value="" disabled selected>Select Tehsil</option>
                    <?php foreach ($tehsils as $tehsilId => $tehsilName): ?>
                        <option value="<?= $tehsilId ?>"><?= htmlspecialchars($tehsilName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="union_council_id" class="form-label">Union Council<span class="text-danger">*</span></label>
                <select name="union_council_id" class="form-select" id="union_council_id" required>
                    <option value="" disabled selected>Select Union Council</option>
                    <?php foreach ($unionCouncils as $unionCouncilId => $unionCouncilName): ?>
                        <option value="<?= $unionCouncilId ?>"><?= htmlspecialchars($unionCouncilName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="add_record" class="btn btn-primary">Add Death Record</button>
    </form>
</main>

<?php
include('include/footer.inc'); 
$conn->close(); // Close database connection
?>

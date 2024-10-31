<?php
session_start(); // Ensure the session is started
$title = "Add Birth Record";
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

// Fetch available fees for birth certificate
$feeQuery = "SELECT fee FROM fees WHERE fee_type = 'Birth Certificate'";
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
    $childName = trim($_POST['child_name']);
    $birthDate = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $motherNIC = trim($_POST['mother_nic']);
    $fatherNIC = trim($_POST['father_nic']);
    $birthPlace = trim($_POST['birth_place']);
    $districtId = $_POST['district_id'];
    $tehsilId = $_POST['tehsil_id'];
    $unionCouncilId = $_POST['union_council_id'];
    $userId = $_SESSION['id']; // Fetching the logged-in user's ID

    // Validate NIC numbers
    if (strlen($motherNIC) !== 13 || strlen($fatherNIC) !== 13) {
        $message = "NIC numbers must be 13 digits.";
    } else {
        // Insert birth record
        $stmt = $conn->prepare("INSERT INTO BirthRecords (ChildName, BirthDate, Gender, MotherNIC, FatherNIC, BirthPlace, PaymentStatus, Fee, DistrictID, TehsilID, UnionCouncilID, UserID) VALUES (?, ?, ?, ?, ?, ?, 'Unpaid', ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssdiisi", $childName, $birthDate, $gender, $motherNIC, $fatherNIC, $birthPlace, $feeAmount, $districtId, $tehsilId, $unionCouncilId, $userId);
        
        if ($stmt->execute()) {
            $message = "Birth record added successfully.";
        } else {
            $message = "Error adding record: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<main class="container my-4">
    <h1 class="m-4 text-center">Add Birth Record</h1>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="child_name" class="form-label">Child Name<span class="text-danger">*</span></label>
                <input type="text" name="child_name" class="form-control" id="child_name" required>
            </div>
            <div class="col-md-6">
                <label for="birth_date" class="form-label">Birth Date<span class="text-danger">*</span></label>
                <input type="date" name="birth_date" class="form-control" id="birth_date" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                <select name="gender" class="form-select" id="gender" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="mother_nic" class="form-label">Mother NIC Number<span class="text-danger">*</span></label>
                <input type="text" name="mother_nic" class="form-control" id="mother_nic" required maxlength="13" minlength="13" pattern="\d{13}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="father_nic" class="form-label">Father NIC Number<span class="text-danger">*</span></label>
                <input type="text" name="father_nic" class="form-control" id="father_nic" required maxlength="13" minlength="13" pattern="\d{13}">
            </div>
            <div class="col-md-6">
                <label for="birth_place" class="form-label">Birth Place<span class="text-danger">*</span></label>
                <input type="text" name="birth_place" class="form-control" id="birth_place" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="fee" class="form-label">Fee for Birth Certificate</label>
                <input type="text" class="form-control" id="fee" value="<?= htmlspecialchars($feeAmount) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label for="district_id" class="form-label">District<span class="text-danger">*</span></label>
                <select name="district_id" class="form-select" id="district_id" required>
                    <option value="" disabled selected>Select District</option>
                    <?php foreach ($districts as $districtId => $districtName): ?>
                        <option value="<?= $districtId ?>"><?= htmlspecialchars($districtName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tehsil_id" class="form-label">Tehsil<span class="text-danger">*</span></label>
                <select name="tehsil_id" class="form-select" id="tehsil_id" required>
                    <option value="" disabled selected>Select Tehsil</option>
                    <?php foreach ($tehsils as $tehsilId => $tehsilName): ?>
                        <option value="<?= $tehsilId ?>"><?= htmlspecialchars($tehsilName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
        <button type="submit" name="add_record" class="btn btn-primary">Add Birth Record</button>
    </form>
</main>

<?php include('include/footer.inc'); ?>

<?php
session_start(); 
$title = "Admin Birth Records Page";
include('include/db_connect.inc'); 
include('include/header.inc'); 
include('include/nav.inc'); 

// Ensure the user is logged in as admin
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM birthrecords WHERE BirthRecordID = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $deleteId);
    $deleteStmt->execute();
    $deleteStmt->close();
    header("Location: admin_birth_records.php");
    exit();
}


// Fetch districts, tehsils, and union councils for filters
$districts = $conn->query("SELECT DistrictID, DistrictName FROM districts");
$tehsils = $conn->query("SELECT TehsilID, TehsilName, DistrictID FROM tehsils");
$union_councils = $conn->query("SELECT UnionCouncilID, UnionCouncilName, TehsilID FROM unioncouncils");

// Handle filters
$whereClauses = [];
$params = [];

if (!empty($_GET['district'])) {
    $whereClauses[] = "br.DistrictID = ?";
    $params[] = $_GET['district'];
}
if (!empty($_GET['tehsil'])) {
    $whereClauses[] = "br.TehsilID = ?";
    $params[] = $_GET['tehsil'];
}
if (!empty($_GET['union_council'])) {
    $whereClauses[] = "br.UnionCouncilID = ?";
    $params[] = $_GET['union_council'];
}

$whereSQL = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Fetch birth records based on filters
$query = "SELECT br.*, d.DistrictName, t.TehsilName, u.UnionCouncilName 
          FROM birthrecords br
          LEFT JOIN districts d ON br.DistrictID = d.DistrictID
          LEFT JOIN tehsils t ON br.TehsilID = t.TehsilID
          LEFT JOIN unioncouncils u ON br.UnionCouncilID = u.UnionCouncilID
          $whereSQL
          ORDER BY BirthRecordID DESC";

$stmt = $conn->prepare($query);

if ($params) {
    $stmt->bind_param(str_repeat("i", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<main class="container my-4">
    <h1 class="text-center">Birth Records</h1>

    <!-- Filter Form -->
    <form method="GET" action="admin_birth_records.php" class="row mb-3">
        <div class="col-md-3">
            <select name="district" class="form-select">
                <option value="">Select District</option>
                <?php while ($district = $districts->fetch_assoc()): ?>
                    <option value="<?= $district['DistrictID'] ?>" <?= ($_GET['district'] ?? '') == $district['DistrictID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($district['DistrictName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="tehsil" class="form-select">
                <option value="">Select Tehsil</option>
                <?php while ($tehsil = $tehsils->fetch_assoc()): ?>
                    <option value="<?= $tehsil['TehsilID'] ?>" <?= ($_GET['tehsil'] ?? '') == $tehsil['TehsilID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tehsil['TehsilName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="union_council" class="form-select">
                <option value="">Select Union Council</option>
                <?php while ($union_council = $union_councils->fetch_assoc()): ?>
                    <option value="<?= $union_council['UnionCouncilID'] ?>" <?= ($_GET['union_council'] ?? '') == $union_council['UnionCouncilID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($union_council['UnionCouncilName']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table id="birthRecordsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Child Name</th>
                    <th>Birth Date</th>
                    <th>Gender</th>
                    <th>Mother NIC</th>
                    <th>Father NIC</th>
                    <th>Birth Place</th>
                    <th>District</th>
                    <th>Tehsil</th>
                    <th>Union Council</th>
                    <th>Payment Status</th>
                    <th>Fee</th>
                    <th>Transaction Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['ChildName']) ?></td>
                        <td><?= htmlspecialchars($record['BirthDate']) ?></td>
                        <td><?= htmlspecialchars($record['Gender']) ?></td>
                        <td><?= htmlspecialchars($record['MotherNIC']) ?></td>
                        <td><?= htmlspecialchars($record['FatherNIC']) ?></td>
                        <td><?= htmlspecialchars($record['BirthPlace']) ?></td>
                        <td><?= htmlspecialchars($record['DistrictName']) ?></td>
                        <td><?= htmlspecialchars($record['TehsilName']) ?></td>
                        <td><?= htmlspecialchars($record['UnionCouncilName']) ?></td>
                        <td><?= htmlspecialchars($record['PaymentStatus']) ?></td>
                        <td><?= htmlspecialchars($record['Fee']) ?></td>
                        <td><img src="../<?= htmlspecialchars($record['TransactionImage']) ?>" alt="Transaction Image" class="img-thumbnail" width="100"></td>
                        <td>
                            <a href="admin_birth_records.php?delete_id=<?= $record['BirthRecordID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button onclick="exportTableToExcel()" class="mt-5 float-right btn btn-success">Export to Excel</button>
    <?php else: ?>
        <div class="alert alert-warning text-center">No records found.</div>
    <?php endif; ?>
</main>

<?php
$result->free();
$stmt->close();
include('include/footer.inc');
?>

<!-- JavaScript for Exporting to PDF and Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    function exportTableToExcel() {
        let table = document.getElementById("birthRecordsTable");
        let workbook = XLSX.utils.table_to_book(table, { sheet: "BirthRecords" });
        XLSX.writeFile(workbook, "BirthRecords.xlsx");
    }

  
</script>

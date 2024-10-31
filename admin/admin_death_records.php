<?php
session_start();
$title = "Admin Death Records Page";
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
    $deleteQuery = "DELETE FROM deathrecords WHERE DeathRecordID = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $deleteId);
    $deleteStmt->execute();
    $deleteStmt->close();
    header("Location: admin_death_records.php");
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
    $whereClauses[] = "dr.DistrictID = ?";
    $params[] = $_GET['district'];
}
if (!empty($_GET['tehsil'])) {
    $whereClauses[] = "dr.TehsilID = ?";
    $params[] = $_GET['tehsil'];
}
if (!empty($_GET['union_council'])) {
    $whereClauses[] = "dr.UnionCouncilID = ?";
    $params[] = $_GET['union_council'];
}

$whereSQL = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Fetch death records based on filters
$query = "SELECT dr.*, d.DistrictName, t.TehsilName, u.UnionCouncilName 
          FROM deathrecords dr
          LEFT JOIN districts d ON dr.DistrictID = d.DistrictID
          LEFT JOIN tehsils t ON dr.TehsilID = t.TehsilID
          LEFT JOIN unioncouncils u ON dr.UnionCouncilID = u.UnionCouncilID
          $whereSQL
          ORDER BY DeathRecordID DESC";

$stmt = $conn->prepare($query);

if ($params) {
    $stmt->bind_param(str_repeat("i", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="container my-4">
    <h1 class=" text-center">Admin Death Records</h1>

<!-- Filter Form -->
<form class="row mb-3" method="GET" action="admin_death_records.php">
    <div class="col-md-3">
        <label for="district">District</label>
        <select class="form-control" id="district" name="district">
            <option value="">All</option>
            <?php while ($district = $districts->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($district['DistrictID']) ?>" <?= isset($_GET['district']) && $_GET['district'] == $district['DistrictID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($district['DistrictName']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="tehsil">Tehsil</label>
        <select class="form-control" id="tehsil" name="tehsil">
            <option value="">All</option>
            <?php while ($tehsil = $tehsils->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($tehsil['TehsilID']) ?>" <?= isset($_GET['tehsil']) && $_GET['tehsil'] == $tehsil['TehsilID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tehsil['TehsilName']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="union_council">Union Council</label>
        <select class="form-control" id="union_council" name="union_council">
            <option value="">All</option>
            <?php while ($uc = $union_councils->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($uc['UnionCouncilID']) ?>" <?= isset($_GET['union_council']) && $_GET['union_council'] == $uc['UnionCouncilID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($uc['UnionCouncilName']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>


    <?php if ($result->num_rows > 0): ?>
        <table id="deathRecordsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Death Record ID</th>
                    <th>Deceased Name</th>
                    <th>Father Name</th>
                    <th>Death Date</th>
                    <th>Cause of Death</th>
                    <th>NIC Number</th>
                    <th>District</th>
                    <th>Tehsil</th>
                    <th>Union Council</th>
                    <th>Payment Status</th>
                    <th>Transaction Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['DeathRecordID']) ?></td>
                        <td><?= htmlspecialchars($record['DeceasedName']) ?></td>
                        <td><?= htmlspecialchars($record['FatherName']) ?></td>
                        <td><?= htmlspecialchars($record['DeathDate']) ?></td>
                        <td><?= htmlspecialchars($record['CauseOfDeath']) ?></td>
                        <td><?= htmlspecialchars($record['NICNumber']) ?></td>
                        <td><?= htmlspecialchars($record['DistrictName']) ?></td>
                        <td><?= htmlspecialchars($record['TehsilName']) ?></td>
                        <td><?= htmlspecialchars($record['UnionCouncilName']) ?></td>
                        <td><?= htmlspecialchars($record['PaymentStatus']) ?></td>
                        <td><img src="../<?= htmlspecialchars($record['TransactionImage']) ?>" alt="Transaction Image" class="img-thumbnail" width="100"></td>
                        <td>
                            <a href="admin_death_records.php?delete_id=<?= $record['DeathRecordID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button onclick="exportTableToExcel()" class="mt-5 float-right btn btn-success">Export to Excel</button>

    <?php else: ?>
        <div class="alert alert-warning text-center">
            <p>No records found for the selected filters.</p>
        </div>
    <?php endif; ?>

    <?php
    // Free result set and close statement
    $result->free();
    $stmt->close();
    ?>
</main>

<?php include('include/footer.inc'); ?>
<!-- JavaScript for Exporting to PDF and Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    function exportTableToExcel() {
        let table = document.getElementById("deathRecordsTable");
        let workbook = XLSX.utils.table_to_book(table, { sheet: "DeathRecords" });
        XLSX.writeFile(workbook, "DeathRecords.xlsx");
    }

  
</script>
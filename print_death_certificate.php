<?php
session_start();
require('fpdf/fpdf.php');
include('include/db_connect.inc'); // Database connection

// Check if the user is logged in and the record ID is provided
if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id'];
$recordId = $_GET['id'];

// Fetch the death record and associated names for district, union council, and tehsil
$query = "
    SELECT dr.*, d.DistrictName, uc.UnionCouncilName, t.TehsilName
    FROM DeathRecords dr
    LEFT JOIN Districts d ON dr.DistrictID = d.DistrictID
    LEFT JOIN UnionCouncils uc ON dr.UnionCouncilID = uc.UnionCouncilID
    LEFT JOIN Tehsils t ON dr.TehsilID = t.TehsilID
    WHERE dr.DeathRecordID = ? AND dr.UserID = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $recordId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$stmt->close();

if (!$record) {
    echo "Record not found or you do not have permission to view this record.";
    exit();
}

// Create PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Death Certificate', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, 'Death Certificate Details', 0, 1, 'C');
$pdf->Ln(10);

$pdf->Cell(50, 10, 'Deceased Name:', 0, 0);
$pdf->Cell(0, 10, $record['DeceasedName'], 0, 1);

$pdf->Cell(50, 10, 'Father Name:', 0, 0);
$pdf->Cell(0, 10, $record['FatherName'], 0, 1);

$pdf->Cell(50, 10, 'Father NIC:', 0, 0);
$pdf->Cell(0, 10, $record['FatherNIC'], 0, 1);

$pdf->Cell(50, 10, 'Death Date:', 0, 0);
$pdf->Cell(0, 10, $record['DeathDate'], 0, 1);

$pdf->Cell(50, 10, 'Cause of Death:', 0, 0);
$pdf->Cell(0, 10, $record['CauseOfDeath'], 0, 1);

$pdf->Cell(50, 10, 'NIC Number:', 0, 0);
$pdf->Cell(0, 10, $record['NICNumber'], 0, 1);

$pdf->Cell(50, 10, 'Death Place:', 0, 0);
$pdf->Cell(0, 10, $record['DeathPlace'], 0, 1);

// Display district, union council, and tehsil names
$pdf->Cell(50, 10, 'District:', 0, 0);
$pdf->Cell(0, 10, $record['DistrictName'], 0, 1);

$pdf->Cell(50, 10, 'Union Council:', 0, 0);
$pdf->Cell(0, 10, $record['UnionCouncilName'], 0, 1);

$pdf->Cell(50, 10, 'Tehsil:', 0, 0);
$pdf->Cell(0, 10, $record['TehsilName'], 0, 1);

$pdf->Ln(10);

// Payment Status and Fee information
$pdf->Cell(50, 10, 'Payment Status:', 0, 0);
$pdf->Cell(0, 10, $record['PaymentStatus'], 0, 1);

$pdf->Cell(50, 10, 'Fee:', 0, 0);
$pdf->Cell(0, 10, $record['Fee'], 0, 1);

$pdf->Output('I', 'Death_Certificate_' . $record['DeathRecordID'] . '.pdf');

// Close the database connection
$conn->close();
?>

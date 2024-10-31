<?php
session_start();
require('include/db_connect.inc'); // Database connection
require('fpdf/fpdf.php'); // Include the FPDF library

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Get the record ID from the query string
if (!isset($_GET['record_id'])) {
    echo "Invalid request.";
    exit();
}
$recordId = $_GET['record_id'];

// Fetch birth record information
$recordQuery = "SELECT * FROM BirthRecords 
                LEFT JOIN districts ON BirthRecords.DistrictID = districts.DistrictID
                LEFT JOIN tehsils ON BirthRecords.TehsilID = tehsils.TehsilID
                LEFT JOIN unioncouncils ON BirthRecords.UnionCouncilID = unioncouncils.UnionCouncilID
                WHERE BirthRecordID = ?";
$stmt = $conn->prepare($recordQuery);
$stmt->bind_param("i", $recordId);
$stmt->execute();
$recordResult = $stmt->get_result();
$record = $recordResult->fetch_assoc();
$stmt->close();

if (!$record) {
    echo "Birth record not found.";
    exit();
}

// Create PDF
class PDF extends FPDF
{
    // Header
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Birth Certificate', 0, 1, 'C');
        $this->Ln(10);
    }

    // Footer
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

// Add record information to the PDF
$pdf->Cell(0, 10, 'Certificate of Birth', 0, 1, 'C');
$pdf->Ln(10);

$pdf->Cell(50, 10, 'Child Name:', 0, 0);
$pdf->Cell(0, 10, $record['ChildName'], 0, 1);

$pdf->Cell(50, 10, 'Birth Date:', 0, 0);
$pdf->Cell(0, 10, $record['BirthDate'], 0, 1);

$pdf->Cell(50, 10, 'Gender:', 0, 0);
$pdf->Cell(0, 10, $record['Gender'], 0, 1);

$pdf->Cell(50, 10, 'Mother NIC:', 0, 0);
$pdf->Cell(0, 10, $record['MotherNIC'], 0, 1);

$pdf->Cell(50, 10, 'Father NIC:', 0, 0);
$pdf->Cell(0, 10, $record['FatherNIC'], 0, 1);

$pdf->Cell(50, 10, 'Birth Place:', 0, 0);
$pdf->Cell(0, 10, $record['BirthPlace'], 0, 1);

$pdf->Cell(50, 10, 'Payment Status:', 0, 0);
$pdf->Cell(0, 10, $record['PaymentStatus'], 0, 1);

$pdf->Cell(50, 10, 'Fee:', 0, 0);
$pdf->Cell(0, 10, $record['Fee'], 0, 1);

$pdf->Cell(50, 10, 'District:', 0, 0);
$pdf->Cell(0, 10, $record['DistrictName'], 0, 1);

$pdf->Cell(50, 10, 'Tehsil:', 0, 0);
$pdf->Cell(0, 10, $record['TehsilName'], 0, 1);

$pdf->Cell(50, 10, 'Union Council:', 0, 0);
$pdf->Cell(0, 10, $record['UnionCouncilName'], 0, 1);

// Output the PDF
$pdf->Output('I', 'Birth_Certificate_' . $recordId . '.pdf');
?>

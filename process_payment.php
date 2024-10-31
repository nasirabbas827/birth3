<?php
session_start();
include('include/db_connect.inc'); // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recordId = $_POST['record_id'];
    $recordType = $_POST['record_type'];

    // Handle file upload
    if (isset($_FILES['transaction_image']) && $_FILES['transaction_image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/"; // Directory to save uploaded images
        $fileName = basename($_FILES['transaction_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        if (in_array($fileType, $allowedTypes)) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['transaction_image']['tmp_name'], $targetFilePath)) {
                // Update payment status based on the record type
                if ($recordType === 'Death Record') {
                    $updateQuery = "UPDATE deathrecords SET PaymentStatus = 'Paid', TransactionImage = ? WHERE DeathRecordID = ?";
                } else if ($recordType === 'Birth Record') {
                    $updateQuery = "UPDATE birthrecords SET PaymentStatus = 'Paid', TransactionImage = ? WHERE BirthRecordID = ?";
                }

                if (isset($updateQuery)) {
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("si", $targetFilePath, $recordId);
                    
                    if ($stmt->execute()) {
                        header("Location: pending_payments.php?status=success");
                    } else {
                        // Handle error
                        header("Location: pending_payments.php?status=error");
                    }
                    $stmt->close();
                } else {
                    // Invalid record type
                    header("Location: pending_payments.php?status=invalid");
                }
            } else {
                // Handle file move error
                header("Location: pending_payments.php?status=file_move_error");
            }
        } else {
            // Invalid file type
            header("Location: pending_payments.php?status=invalid_file_type");
        }
    } else {
        // Handle file upload error
        header("Location: pending_payments.php?status=file_upload_error");
    }
}

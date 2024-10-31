<?php
$title = "Register Page";
include('include/db_connect.inc'); // Assumes $conn is defined here
include('include/header.inc'); 

// Initialize message variables
$errorMsg = $successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $bio = trim($_POST['bio']);
    $usertype = "User"; // Set default usertype

    // Check required fields and confirm password
    if (!$username || !$email || !$password || !$confirmPassword) {
        $errorMsg = "All fields marked with * are required.";
    } elseif ($password !== $confirmPassword) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Encrypt password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database with prepared statements
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, bio, usertype) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashedPassword, $bio, $usertype);

        if ($stmt->execute()) {
            $successMsg = "Registration successful! You can now log in.";
        } else {
            $errorMsg = "An error occurred. Please try again later.";
        }
        $stmt->close();
    }
}

include('include/nav.inc'); 
?>

<main class="container my-5">
    <h1 class="text-center mb-4">Register</h1>
    <p class="text-center mb-4">Create your account here</p>

    <!-- Display messages -->
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php elseif ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <form class="needs-validation" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="username" class="form-label">Username<span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                <div class="invalid-feedback">Please choose a username.</div>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" id="password" required>
                <div class="invalid-feedback">Please provide a password.</div>
            </div>

            <div class="col-md-6">
                <label for="confirm-password" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" id="confirm-password" required>
                <div class="invalid-feedback">Please confirm your password.</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea class="form-control" name="bio" id="bio" rows="3"><?= htmlspecialchars($bio ?? '') ?></textarea>
        </div>

        <div class="text-center">
            <button class="btn btn-primary" type="submit">Register</button>
            <button class="btn btn-outline-secondary" type="reset">Clear</button>
        </div>
    </form>
</main>


<?php include('include/footer.inc'); ?>

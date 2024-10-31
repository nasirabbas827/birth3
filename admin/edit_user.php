<?php
$title = "Edit User Page";
include('include/db_connect.inc'); // Assumes $conn is defined here
include('include/header.inc');

// Initialize message variables
$errorMsg = $successMsg = "";

// Check if user_id is set
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch the user details for the given user_id
    $stmt = $conn->prepare("SELECT username, email, bio, usertype FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        $errorMsg = "User not found.";
    }
    $stmt->close();
} else {
    $errorMsg = "Invalid request.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($user_id)) {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $bio = trim($_POST['bio']);
    $usertype = $_POST['usertype']; // Capture usertype
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check required fields and confirm password if provided
    if (!$username || !$email) {
        $errorMsg = "Username and Email are required.";
    } elseif ($password && ($password !== $confirmPassword)) {
        $errorMsg = "Passwords do not match.";
    } else {
        // Prepare query for updating user
        if ($password) {
            // If password is set, hash it and update with password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ?, usertype = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $username, $email, $bio, $usertype, $hashedPassword, $user_id);
        } else {
            // Update without password
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ?, usertype = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $username, $email, $bio, $usertype, $user_id);
        }

        // Execute update query
        if ($stmt->execute()) {
            $successMsg = "User updated successfully.";
            header("Location: adduser.php");
            exit;
        } else {
            $errorMsg = "An error occurred. Please try again later.";
        }
        $stmt->close();
    }
}

include('include/nav.inc');
?>

<main class="container my-5">
    <h1 class="text-center mb-4">Edit User</h1>

    <!-- Display messages -->
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php elseif ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <form class="needs-validation" action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . "?id=" . $user_id); ?>" method="POST" novalidate>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="username" class="form-label">Username<span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                <div class="invalid-feedback">Please choose a username.</div>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea class="form-control" name="bio" id="bio" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="usertype" class="form-label">User Type<span class="text-danger">*</span></label>
                <select class="form-control" name="usertype" id="usertype" required>
                    <option value="User" <?= ($user['usertype'] === 'User') ? 'selected' : '' ?>>User</option>
                    <option value="Admin" <?= ($user['usertype'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                </select>
                <div class="invalid-feedback">Please select a user type.</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" id="password">
                <div class="form-text">Leave blank if you don't want to change the password.</div>
            </div>

            <div class="col-md-6">
                <label for="confirm-password" class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" id="confirm-password">
            </div>
        </div>

        <div class="text-center">
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
    </form>
</main>

<?php include('include/footer.inc'); ?>

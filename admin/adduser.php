<?php
$title = "User Management";
include('include/db_connect.inc'); // Assumes $conn is defined here
include('include/header.inc'); 

// Initialize message variables
$errorMsg = $successMsg = "";

// Handle registration of new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $bio = trim($_POST['bio']);
    $usertype = $_POST['usertype']; // Capture usertype from form

    // Check required fields and confirm password
    if (!$username || !$email || !$password || !$confirmPassword || !$usertype) {
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
            $successMsg = "User added successfully.";
        } else {
            $errorMsg = "An error occurred. Please try again later.";
        }
        $stmt->close();
    }
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $successMsg = "User deleted successfully.";
    } else {
        $errorMsg = "An error occurred while deleting the user.";
    }
    $stmt->close();
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, username, email, bio, usertype FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include('include/nav.inc'); 
?>

<main class="container my-5 mb-5" style="height:170vh;">
    <h1 class="text-center mb-4">User Management</h1>

    <!-- Display messages -->
    <?php if ($errorMsg): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($errorMsg) ?>
    </div>
    <?php elseif ($successMsg): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($successMsg) ?>
    </div>
    <?php endif; ?>

    <!-- User Registration Form -->
    <h2 class="mt-5">Add User</h2>
    <form class="needs-validation" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
        <input type="hidden" name="action" value="register">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="username" class="form-label">Username<span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" id="username"
                    value="<?= htmlspecialchars($username ?? '') ?>" required>
                <div class="invalid-feedback">Please choose a username.</div>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="email"
                    value="<?= htmlspecialchars($email ?? '') ?>" required>
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
                <label for="confirm-password" class="form-label">Confirm Password<span
                        class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" id="confirm-password" required>
                <div class="invalid-feedback">Please confirm your password.</div>
            </div>
        </div>
        <div class="row mb-3">



            <div class="col-md-6">
                <label for="usertype" class="form-label">User Type<span class="text-danger">*</span></label>
                <select class="form-control" name="usertype" id="usertype" required>
                    <option value="User" <?=(isset($usertype) && $usertype==='User' ) ? 'selected' : '' ?>>User</option>
                    <option value="Admin" <?=(isset($usertype) && $usertype==='Admin' ) ? 'selected' : '' ?>>Admin
                    </option>
                </select>
                <div class="invalid-feedback">Please select a user type.</div>
            </div>
            <div class="col-md-6">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" name="bio" id="bio" rows="3"><?= htmlspecialchars($bio ?? '') ?></textarea>
            </div>

        </div>

        <div class="text-center">
            <button class="btn btn-primary" type="submit">Add</button>
        </div>
    </form>

    <!-- User List -->
    <h2 class="m-5 text-center">Registered Users</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Bio</th>
                <th>User Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($user['id']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($user['username']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($user['email']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($user['bio']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($user['usertype']) ?>
                </td>
                <td>
                    <a href="edit_user.php?id=<?= htmlspecialchars($user['id']) ?>"
                        class="btn btn-warning btn-sm">Edit</a>
                    <a href="#" class="btn btn-danger btn-sm"
                        onclick="confirmDelete(<?= htmlspecialchars($user['id']) ?>)">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = "<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>?delete=" + userId;
        }
    }
</script>

<?php include('include/footer.inc'); ?>
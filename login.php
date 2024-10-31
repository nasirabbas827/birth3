<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = "Login Page";
include('include/db_connect.inc'); // Assumes $conn is defined here
include('include/header.inc'); 

// Initialize message variables
$errorMsg = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $errorMsg = "Please provide both email and password.";
    } else {
        // Retrieve user record by email, including usertype
        $stmt = $conn->prepare("SELECT id, username, password, usertype FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $username, $hashedPassword, $usertype);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashedPassword)) {
                // Set session variables
                $_SESSION["id"] = $id;
                $_SESSION['username'] = $username;

                // Redirect based on usertype
                if ($usertype === 'Admin') {
                    header("Location: admin/home.php"); // Redirect to admin home page
                } else {
                    header("Location: user.php"); // Redirect to user profile page
                }
                exit();
            } else {
                $errorMsg = "Invalid email or password.";
            }
        } else {
            $errorMsg = "No account found with that email.";
        }
        $stmt->close();
    }
}

include('include/nav.inc'); 
?>

<main class="container my-5">
    <h1 class="text-center mb-4">Login</h1>
    <p class="text-center mb-4">Please enter your credentials to login</p>

    <!-- Display error message -->
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form class="needs-validation" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" id="email" required>
            <div class="invalid-feedback">Please provide a valid email.</div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" id="password" required>
            <div class="invalid-feedback">Please provide your password.</div>
        </div>

        <div class="text-center">
            <button class="btn btn-primary" type="submit">Login</button>
            <button class="btn btn-outline-secondary" type="reset">Clear</button>
        </div>
    </form>

    <div class="text-center mt-3">
        <p>Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
    </div>
</main>

<?php include('include/footer.inc'); ?>

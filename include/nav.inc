<?php
// Start session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
            <a class="navbar-brand" href="index.php">
    <i class="fas fa-baby"></i> 
</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <?php
                if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
                    echo '<li class="nav-item"><a class="nav-link" href="user.php">Dashboard</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="view_birth_records.php">Birth Records</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="view_death_records.php">Death Records</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                }
                ?>
            </ul>
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="keyword" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<?php
// Start session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="home.php">
    <i class="fas fa-baby"></i> 
</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="adduser.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="managedistricts.php">Districts</a></li>               
                <li class="nav-item"><a class="nav-link" href="manage_tehsils.php">Tehsils</a></li>               
                <li class="nav-item"><a class="nav-link" href="manage_union_councils.php">Union Councils</a></li>               
                <li class="nav-item"><a class="nav-link" href="manage_fees.php">Fees</a></li>               
                <li class="nav-item"><a class="nav-link" href="admin_birth_records.php">Birth Records</a></li>               
                <li class="nav-item"><a class="nav-link" href="admin_death_records.php">Death Records</a></li>               
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>               
            </ul>
            <form class="d-flex" method="GET" action="">
                <input class="form-control me-2" type="search" name="keyword" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

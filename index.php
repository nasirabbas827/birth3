<?php
$title = "Index Page";
include('include/db_connect.inc');
include('include/header.inc'); 
include('include/nav.inc'); 
?>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="images/baby.jpg" alt="Tracking Birth-Death Ratio" class="img-fluid rounded carousel-image">
                            <div class="carousel-caption d-none d-md-block">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 text-center text-md-start">
                <h1 class="hero-title text-center">Population Tracker</h1>
                <h3 class="hero-subtitle text-center">Online Portal for Tracking Birth-Death Ratio of Population</h3>
            </div>
        </div>
    </div>
</section>

<section class="search-section">
    <div class="container">
        <form method="GET" action="login.php" class="row">
            <div class="col-md-8 mb-3 mb-md-0">
                <input type="text" name="keyword" class="form-control" placeholder="Search by NIC or Record ID...">
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <select name="record_type" class="form-select">
                    <option value="">Select Record Type</option>
                    <option value="birth">Birth Records</option>
                    <option value="death">Death Records</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</section>

<section class="content-section py-5">
    <div class="container">
        <h1 class="mb-4 text-center">About the Population Tracking Portal</h1>
        <p>This online portal is designed to help track birth and death ratios across districts, tehsils, and union councils, supporting data-driven decisions for socio-economic planning. Through our platform, administrators at Union Council, Tehsil, and District levels can enter new birth and death records, authenticate records, and facilitate the public in obtaining official certificates.</p>

        <h3 class="mb-3">Project Overview</h3>
        <p>Birth and death ratios provide insights into population growth trends and patterns that are critical for policymakers and healthcare providers. Our portal replaces the traditional manual record-keeping system with a centralized digital solution, simplifying access to demographic data and enabling stakeholders to respond effectively to changes in birth-death dynamics.</p>

        <h3 class="mb-3">Key Features</h3>
        <ul>
            <li>Easy record entry for births and deaths.</li>
            <li>Digital authentication for end-users and administrators at various administrative levels.</li>
            <li>Online payment integration for certificate issuance.</li>
            <li>Data analytics for tracking trends in birth-death ratios.</li>
            <li>Access to demographic reports within specific regions.</li>
        </ul>

        <h3 class="mb-3">How It Works</h3>
        <p>Users can log in with their NIC number to record births and deaths or request certificates. Administrators can manage districts, tehsils, and union councils, ensuring accurate and authenticated record-keeping at every level.</p>
    </div>
</section>

<?php include('include/footer.inc'); ?>

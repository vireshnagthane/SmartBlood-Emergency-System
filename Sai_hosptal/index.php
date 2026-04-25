<?php
session_start();
include 'db.php';

// Fetch quick stats
$total_donors = $conn->query("SELECT COUNT(*) as count FROM donors")->fetch_assoc()['count'] ?? 0;
$active_requests = $conn->query("SELECT COUNT(*) as count FROM blood_requests WHERE status='Pending'")->fetch_assoc()['count'] ?? 0;
$available_units = $conn->query("SELECT SUM(units_available) as count FROM blood_inventory")->fetch_assoc()['count'] ?? 0;

// Fetch Urgent Live Requests for Ticker
$urgent_query = $conn->query("SELECT r.blood_group, r.city, h.name FROM blood_requests r JOIN hospitals h ON r.hospital_id = h.id WHERE r.status='Pending' AND r.urgency_level='High' LIMIT 5");
$urgencies = [];
while($r = $urgent_query->fetch_assoc()){
    $urgencies[] = "🚨 {$r['blood_group']} immediately needed at {$r['name']} ({$r['city']})";
}
$ticker_text = count($urgencies) > 0 ? implode(" &nbsp;&nbsp;|&nbsp;&nbsp; ", $urgencies) : "✅ No active high-urgency emergencies right now.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Blood Emergency System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="redesigned-body">

<!-- Live Emergency Ticker -->
<div class="ticker-wrap">
    <div class="ticker">
        <?= $ticker_text ?>
    </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top border-bottom navbar-light bg-white py-2">
    <div class="container">
        <a class="navbar-brand text-danger fw-bold fs-4 d-flex align-items-center" href="index.php">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            SmartBlood
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-medium align-items-center">
                <li class="nav-item"><a class="nav-link px-3 text-muted nav-hover" href="signup.php">Register</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-muted nav-hover" href="emergency_search.php">Find Donors</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-danger fw-semibold nav-hover" href="request_blood.php">Emergency</a></li>
                <li class="nav-item"><a class="nav-link ps-3 text-muted nav-hover" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-redesigned text-center py-5">
    <div class="container py-5 mt-4">
        <h1 class="display-4 fw-bolder mb-3 hero-title text-dark-blue">Blood Donor Management System</h1>
        <p class="lead mb-5 text-muted mx-auto" style="max-width: 700px; font-size: 1.1rem; line-height: 1.6;">Connecting blood donors with hospitals across the network. Save lives by making blood donation faster, more accessible, and efficient through our digital platform.</p>
        
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="signup.php" class="btn btn-danger btn-lg px-4 py-2 hero-btn shadow-sm fs-6">Register as Donor</a>
            <a href="emergency_search.php" class="btn btn-outline-secondary btn-lg px-4 py-2 hero-btn-outline fs-6 bg-white">Find Blood Donors</a>
            <a href="request_blood.php" class="btn btn-danger btn-lg px-4 py-2 hero-btn shadow-sm fs-6">Emergency Alert</a>
        </div>
    </div>
</section>

<section class="stats-row border-top border-bottom py-5 bg-white">
    <div class="container text-center py-2">
        <div class="row g-4 d-flex align-items-center">
            <div class="col-md-3 stat-item">
                <h2 class="fw-bolder mb-1" style="color:#d90429; font-size: 2.2rem;"><span class="stat-number" data-val="670">0</span><span class="fs-4">K+</span></h2>
                <p class="text-muted small fw-medium mb-0">Blood Units Needed Annually</p>
            </div>
            <div class="col-md-3 stat-item border-start-md">
                <h2 class="fw-bolder mb-1" style="color:#3a86ff; font-size: 2.2rem;"><span class="stat-number" data-val="72">0</span><span class="fs-4">%</span></h2>
                <p class="text-muted small fw-medium mb-0">Maternal Deaths Preventable</p>
            </div>
            <div class="col-md-3 stat-item border-start-md">
                <h2 class="fw-bolder mb-1" style="color:#2b9348; font-size: 2.2rem;"><span class="stat-number" data-val="10">0</span><span class="fs-5">/1000</span></h2>
                <p class="text-muted small fw-medium mb-0">WHO Recommended Donation Rate</p>
            </div>
            <div class="col-md-3 stat-item border-start-md">
                <h2 class="fw-bolder mb-1" style="color:#9d4edd; font-size: 2.2rem;"><span class="stat-number" data-val="50">0</span><span class="fs-4">%</span></h2>
                <p class="text-muted small fw-medium mb-0">Current Collection Gap</p>
            </div>
        </div>
    </div>
</section>

<section class="features-section py-5 my-5 rounded-4" style="background: linear-gradient(180deg, #fafbfc 0%, #f1f4f9 100%); margin:0 15px;">
    <div class="container py-4">
        <h2 class="text-center fw-bolder mb-5 text-dark-blue fs-3">How SmartBlood Saves Lives</h2>
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="feature-card p-4 rounded-4 bg-white border-subtle h-100 shadow-sm-hover">
                    <div class="icon-wrap mb-4 text-danger">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h5 class="fw-bold mb-3 fs-6">Donor Registration</h5>
                    <p class="text-muted small lh-lg mb-0 text-truncate-3">Quick and secure registration with comprehensive donor profiles. Register with personal details, blood group, and location to become part of our life-saving network.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="feature-card p-4 rounded-4 bg-white border-subtle h-100 shadow-sm-hover">
                    <div class="icon-wrap mb-4 text-primary">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <h5 class="fw-bold mb-3 fs-6">Location-Based Search</h5>
                    <p class="text-muted small lh-lg mb-0 text-truncate-3">Find nearby donors instantly using our geographic mapping system. Hospitals can quickly locate compatible donors in their area, reducing response time in emergencies.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="feature-card p-4 rounded-4 bg-white border-subtle h-100 shadow-sm-hover">
                    <div class="icon-wrap mb-4 text-warning">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffb703" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    </div>
                    <h5 class="fw-bold mb-3 fs-6">Emergency Alerts</h5>
                    <p class="text-muted small lh-lg mb-0 text-truncate-3">Instant SMS notifications to nearby compatible donors. Send emergency alerts to multiple donors simultaneously when blood is urgently needed.</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-4">
                <div class="feature-card p-4 rounded-4 bg-white border-subtle h-100 shadow-sm-hover">
                    <div class="icon-wrap mb-4 text-success">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#38b000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <h5 class="fw-bold mb-3 fs-6">Donation History</h5>
                    <p class="text-muted small lh-lg mb-0 text-truncate-3">Track donation eligibility and maintain complete records. Automated tracking ensures donor safety by monitoring donation intervals and health status.</p>
                </div>
            </div>
            <!-- Feature 5 -->
            <div class="col-md-4">
                <div class="feature-card p-4 rounded-4 bg-white border-subtle h-100 shadow-sm-hover">
                    <div class="icon-wrap mb-4" style="color: #9d4edd;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h5 class="fw-bold mb-3 fs-6">Secure & Private</h5>
                    <p class="text-muted small lh-lg mb-0 text-truncate-3">GDPR-compliant data protection with donor consent management. Your personal information is protected with enterprise-grade security and privacy controls.</p>
                </div>
            </div>
            <!-- Feature 6 -->
            <div class="col-md-4">
                <div class="feature-card p-4 rounded-4 bg-white border-subtle h-100 shadow-sm-hover">
                    <div class="icon-wrap mb-4 text-danger">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                    <h5 class="fw-bold mb-3 fs-6">Save Lives</h5>
                    <p class="text-muted small lh-lg mb-0 text-truncate-3">Every donation can save up to three lives. Join our digital blood donation network and help reduce preventable deaths across the nation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-banner text-center text-white py-5" style="background-color: #e63946;">
    <div class="container py-5">
        <h2 class="fw-bolder mb-3 fs-2">Ready to Save Lives?</h2>
        <p class="mb-4 text-white-50" style="font-size: 1.05rem;">Join thousands of donors across our mission to ensure no life is lost due to blood shortage.</p>
        <a href="signup.php" class="btn btn-light text-danger fw-bold px-4 py-2 mt-2 shadow-sm fs-6 border-0" style="border-radius: 6px;">Register Now</a>
    </div>
</section>

<footer class="footer-dark text-center py-4" style="background-color: #1a1e29; color: #a4b0be;">
    <div class="container py-2">
        <div class="text-white fw-bold mb-3 d-flex justify-content-center align-items-center fs-5">
            <svg class="text-danger me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            SmartBlood Network
        </div>
        <p class="mb-0 small">© 2026 Blood Donor Management System. Saving lives through technology.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Interactive counter animation for life facts
    document.addEventListener("DOMContentLoaded", () => {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        const animateValue = (element, start, end, duration) => {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                // easeOutQuad
                const easeOut = 1 - (1 - progress) * (1 - progress);
                element.innerHTML = Math.floor(easeOut * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const finalVal = parseInt(el.getAttribute('data-val'), 10);
                    animateValue(el, 0, finalVal, 2000);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => observer.observe(stat));
    });
</script>
</body>
</html>


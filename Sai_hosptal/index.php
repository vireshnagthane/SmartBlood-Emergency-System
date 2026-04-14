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
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Blood Emergency System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .ticker-wrap {
            width: 100%;
            background-color: var(--danger, #dc3545);
            color: white;
            padding: 8px 0;
            overflow: hidden;
            font-weight: 600;
        }
        .ticker {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker 15s linear infinite;
        }
        @keyframes ticker {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }
        .vs-card {
            border-radius: 15px;
            padding: 30px;
            height: 100%;
        }
        .struggle-card {
            background-color: #f8dbdb; /* Light red */
            border-left: 6px solid #dc3545;
        }
        .solution-card {
            background-color: #d1f2eb; /* Light green */
            border-left: 6px solid #198754;
        }
        [data-theme='dark'] .struggle-card { background-color: #3b1c1c; }
        [data-theme='dark'] .solution-card { background-color: #17382d; }
    </style>
</head>
<body>

<!-- Live Emergency Ticker -->
<div class="ticker-wrap">
    <div class="ticker">
        <?= $ticker_text ?>
    </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span style="font-size: 24px;">🩸</span>
            SmartBlood
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="donor_login.php">Donor Portal</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Hospital Portal</a></li>
                <li class="nav-item ms-3">
                    <button class="dark-mode-toggle" onclick="toggleDarkMode()">🌙</button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section text-center fade-in">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Find Blood Instantly in Emergencies</h1>
        <p class="lead mb-5 text-muted">Intelligent matching, real-time availability, and absolute precision. Connecting life-saving donors to those who have seconds to spare.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="signup.php" class="btn btn-primary btn-lg">Register as Donor</a>
            <!-- Will redirect to hospital login unless logged in -->
            <a href="login.php" class="btn btn-outline-danger btn-lg fw-bold border-2 pulse">🚨 I Need Blood Now</a>
        </div>
    </div>
</section>

<!-- The Struggle vs Solution Section -->
<div class="container mb-5 fade-in">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Why finding blood is historically terrifying...</h2>
        <p class="text-muted">And exactly how we fixed it.</p>
    </div>
    <div class="row g-4 align-items-stretch">
        <div class="col-md-5">
            <div class="vs-card struggle-card shadow-sm">
                <h4 class="fw-bold text-danger mb-4">❌ Old World Struggle</h4>
                <ul class="list-unstyled">
                    <li class="mb-3">☎️ <strong>Blind Panicking:</strong> Calling 50 unknown numbers off an outdated WhatsApp list.</li>
                    <li class="mb-3">✈️ <strong>Location Mismatch:</strong> Donor picks up, but they are physically out of town.</li>
                    <li class="mb-3">🩸 <strong>Health Ineligible:</strong> Donor arrives but is rejected because they donated 40 days ago (90-day cooldown).</li>
                    <li>⏳ <strong>Fatal Delays:</strong> Average search time takes 4-12 hours.</li>
                </ul>
            </div>
        </div>
        <div class="col-md-2 d-flex align-items-center justify-content-center">
            <h1 class="fw-bold" style="color: var(--primary-color);">VS</h1>
        </div>
        <div class="col-md-5">
            <div class="vs-card solution-card shadow-sm">
                <h4 class="fw-bold text-success mb-4">✅ The Next-Gen Match</h4>
                <ul class="list-unstyled">
                    <li class="mb-3">📍 <strong>GPS Haversine Math:</strong> System instantly calculates donors closest to you within a precise km radius.</li>
                    <li class="mb-3">🟢 <strong>Live Switch:</strong> We strictly route requests to donors who toggled "Available" this week.</li>
                    <li class="mb-3">🧠 <strong>90-Day Math:</strong> Profiles are hard-locked if they donated recently, so you never waste a call.</li>
                    <li>⚡ <strong>Instance Pings:</strong> 1-Click Blast notifies all qualified matches instantly.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5 fade-in">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-box">
                <div class="stat-icon">🩸</div>
                <h2 class="stat-value"><?= $total_donors ?></h2>
                <div class="stat-label">Registered Donors</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box">
                <div class="stat-icon">🚨</div>
                <h2 class="stat-value"><?= $active_requests ?></h2>
                <div class="stat-label">Active Requests</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box">
                <div class="stat-icon">💖</div>
                <h2 class="stat-value"><?= $available_units ?></h2>
                <div class="stat-label">Available Units</div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        if (html.getAttribute('data-theme') === 'dark') {
            html.setAttribute('data-theme', 'light');
        } else {
            html.setAttribute('data-theme', 'dark');
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Floating About Button -->
<button class="btn btn-dark shadow rounded-circle floating-about-btn" data-bs-toggle="modal" data-bs-target="#aboutModal" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1000; font-size: 24px; border: 2px solid white;">
    ??
</button>

<!-- About Modal -->
<div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border-top: 5px solid var(--primary-color);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">?? SmartBlood tech specs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-4">A complete breakdown of the Next-Gen features powering this Blood Donor Management System.</p>
        <ul class="list-unstyled">
            <li class="mb-3">?? <strong>Haversine Geo-Routing:</strong> MySQL mathematically ranks donors based on precise physical distance (latitude/longitude).</li>
            <li class="mb-3">?? <strong>Emergency Blast UI:</strong> Red-alert layout shifting, dynamic countdown timers, and JS-based event timelines.</li>
            <li class="mb-3">?? <strong>Live Activity Tracking:</strong> Checks timestamps dynamically to broadcast if a donor is Online or Offline.</li>
            <li class="mb-3">?? <strong>Smart Eligibility Check:</strong> Enforces a strict 90-day cooldown database lock after donations.</li>
            <li class="mb-2">?? <strong>Gamification Engine:</strong> Awards "Hero Points" updating donor ranks to Bronze, Silver, or Hero dynamically.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
</body>
</html>


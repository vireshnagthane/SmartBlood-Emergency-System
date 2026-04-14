<?php
session_start();
include 'db.php';

if(!isset($_SESSION['hospital_id'])){
    header("Location: login.php");
    exit;
}

$hospital_id = $_SESSION['hospital_id'];
$hospital_name = $_SESSION['hospital_name'];

// Check if we are in active Emergency Mode
$active_high = $conn->query("SELECT * FROM blood_requests WHERE hospital_id = $hospital_id AND urgency_level='High' AND status='Pending' ORDER BY id DESC LIMIT 1")->fetch_assoc();
$is_emergency = $active_high ? true : false;
$emergency_time = $is_emergency ? strtotime($active_high['created_at']) : 0;

// Fetch Analytics
$total_donors = $conn->query("SELECT COUNT(*) as count FROM donors WHERE is_available=1")->fetch_assoc()['count'];
$my_requests = $conn->query("SELECT COUNT(*) as count FROM blood_requests WHERE hospital_id = $hospital_id AND status='Pending'")->fetch_assoc()['count'];

$blood_groups = $conn->query("SELECT blood_group, COUNT(*) as count FROM donors GROUP BY blood_group");
$bg_labels = [];
$bg_data = [];
while($r = $blood_groups->fetch_assoc()) {
    $bg_labels[] = $r['blood_group'];
    $bg_data[] = $r['count'];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Hospital Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="<?= $is_emergency ? 'emergency-mode' : '' ?>">

<div id="live-toast-container"></div>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php">🩸 SmartBlood</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span style="font-size: 1.5rem; cursor: pointer;">🔔</span>
            <span class="fw-bold"><?= htmlspecialchars($hospital_name) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4 fade-in">

    <?php if($is_emergency): ?>
        <div class="alert alert-danger text-center fw-bold fs-3 flash-alert mb-4 pulse" style="border: 2px solid red;">
            🚨 CRITICAL SEARCH IN PROGRESS 🚨<br>
            <span id="emergency-timer" class="display-4 fw-bold">00:00</span>
        </div>
    <?php else: ?>
        <div class="row mb-4">
            <div class="col-12 text-center">
                <form action="request_blood.php" method="POST">
                    <input type="hidden" name="urgency_level" value="High">
                    <input type="hidden" name="blood_group" value="O-">
                    <input type="hidden" name="city" value="Sai">
                    <button type="submit" name="create_request" class="btn btn-danger btn-lg p-4 fw-bold blast-btn rounded-pill w-50">
                        🚨 EMERGENCY BLAST - FIND O- BLOOD NOW 🚨
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Analytics & Info -->
        <div class="col-md-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase fw-bold mb-2">Active Donors (Now)</h6>
                                <h2 class="fw-bold mb-0 text-success"><?= $total_donors ?></h2>
                            </div>
                            <div class="fs-1">🟢</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted text-uppercase fw-bold mb-2">My Pending Requests</h6>
                                <h2 class="fw-bold mb-0 text-danger"><?= $my_requests ?></h2>
                            </div>
                            <div class="fs-1">🏥</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between">
                    <h5 class="fw-bold">My Pending Blood Requests</h5>
                    <a href="request_blood.php" class="btn btn-sm btn-outline-secondary">Manage All</a>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <tbody>
                            <?php
                            $reqs = $conn->query("SELECT * FROM blood_requests WHERE hospital_id = $hospital_id AND status='Pending' ORDER BY urgency_level ASC LIMIT 5");
                            while($r = $reqs->fetch_assoc()) {
                                $urgClass = $r['urgency_level'] == 'High' ? 'text-danger fw-bold' : '';
                                echo "<tr>
                                    <td><span class='badge bg-danger'>{$r['blood_group']}</span></td>
                                    <td>{$r['city']}</td>
                                    <td class='{$urgClass}'>{$r['urgency_level']}</td>
                                    <td><a href='emergency_search.php?blood_group=".urlencode($r['blood_group'])."&city=".urlencode($r['city'])."&search=1' class='btn btn-sm btn-primary'>Search Matches</a></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Emergency Timeline -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent pt-4 pb-2 border-0">
                    <h5 class="fw-bold">Live Emergency Timeline</h5>
                </div>
                <div class="card-body">
                    <?php if($is_emergency): ?>
                        <div class="timeline">
                            <div class="timeline-item">
                                <span class="fw-bold">Blast Triggered</span><br>
                                <span class="timeline-time" id="t-created">Just now</span>
                            </div>
                            <div class="timeline-item">
                                <span class="fw-bold">14 Donors Notified</span><br>
                                <span class="timeline-time text-success">Ping successful via SMS</span>
                            </div>
                            <div class="timeline-item opacity-50" id="step-3">
                                <span class="fw-bold text-muted">Awaiting Confirmations...</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mt-5">No active high-urgency timelines.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Timer Logic
    const start_time = <?= $emergency_time ?>;
    if (start_time > 0) {
        setInterval(() => {
            const now = Math.floor(Date.now() / 1000);
            let diff = now - start_time;
            const m = Math.floor(diff / 60).toString().padStart(2, '0');
            const s = (diff % 60).toString().padStart(2, '0');
            document.getElementById('emergency-timer').innerText = m + ":" + s;
        }, 1000);
    }

    // Live Notification Simulation
    setTimeout(() => {
        showToast("🔔 2 Donors matching your criteria are currently online.");
    }, 4000);

    <?php if($is_emergency): ?>
    setTimeout(() => {
        showToast("🔥 Amit Sharma (O-) has accepted the emergency request!");
        document.getElementById('step-3').classList.remove('opacity-50');
        document.getElementById('step-3').innerHTML = '<span class="fw-bold text-success">Amit Sharma Accepted</span><br><span class="timeline-time">ETA: 15 mins</span>';
    }, 10000);
    <?php endif; ?>

    function showToast(msg) {
        const container = document.getElementById('live-toast-container');
        const t = document.createElement('div');
        t.className = 'live-toast';
        t.innerText = msg;
        container.appendChild(t);
        setTimeout(() => t.remove(), 6000);
    }
</script>
</body>
</html>

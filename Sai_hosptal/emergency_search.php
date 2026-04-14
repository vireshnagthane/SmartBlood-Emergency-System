<?php
session_start();
include 'db.php';

if(!isset($_SESSION['hospital_id'])){
    header("Location: login.php");
    exit;
}

// Get Hospital coordinates for distance calculation
$hospital_id = $_SESSION['hospital_id'];
$hosp = $conn->query("SELECT latitude, longitude FROM hospitals WHERE id=$hospital_id")->fetch_assoc();
$h_lat = $hosp['latitude'] ?? 24.5300;
$h_lon = $hosp['longitude'] ?? 81.3000;

$search_results = [];
$searched = false;

if (isset($_GET['search'])) {
    $blood_group = $_GET['blood_group'];
    $city = $_GET['city'];

    // Haversine implementation in SQL:
    // Distance in km = 6371 * acos(cos(radians(h_lat)) * cos(radians(lat)) * cos(radians(lon) - radians(h_lon)) + sin(radians(h_lat)) * sin(radians(lat)))
    
    $stmt = $conn->prepare("
        SELECT id, name, mobile, blood_group, city, last_donation_date, last_active_time, is_available,
        COALESCE(
            6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))), 
        0) AS distance_km
        FROM donors
        WHERE blood_group = ? 
        AND city LIKE ?
        AND is_available = 1
        AND (last_donation_date IS NULL OR DATEDIFF(CURDATE(), last_donation_date) >= 90)
        ORDER BY distance_km ASC, last_active_time DESC
        LIMIT 20
    ");
    
    $city_param = "%" . $city . "%";
    $stmt->bind_param("dddss", $h_lat, $h_lon, $h_lat, $blood_group, $city_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        // Calculate Match Score
        $dist_score = max(0, 100 - ($row['distance_km'] * 2)); // Assume 50km radius drops score to 0
        $active_mins = (time() - strtotime($row['last_active_time'])) / 60;
        $recency_score = $active_mins < 60 ? 100 : ($active_mins < 1440 ? 50 : 10);
        
        $row['match_score'] = round(($dist_score * 0.70) + ($recency_score * 0.30));
        
        // Status Check
        if ($active_mins < 5) $row['status_icon'] = '🟢 Online';
        else if ($active_mins < 60) $row['status_icon'] = '🟡 Recently Active';
        else $row['status_icon'] = '🔴 Offline';
        
        $search_results[] = $row;
    }
    $stmt->close();
    
    // Sort by Match Score Descending
    usort($search_results, function($a, $b) { return $b['match_score'] <=> $a['match_score']; });
    
    $searched = true;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Smart Emergency Search</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">🩸 SmartBlood Admin</a>
        <div class="ms-auto">
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">← Back to Dashboard</a>
        </div>
    </div>
</nav>

<div class="container mt-4 fade-in">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-danger mb-3">🚨 Advanced Emergency Search</h4>
            <form method="get" action="">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Blood Group</label>
                        <select class="form-select" name="blood_group" required>
                            <option value="">Select</option>
                            <option <?= (isset($_GET['blood_group']) && $_GET['blood_group']=='A+')? 'selected':'' ?>>A+</option>
                            <option <?= (isset($_GET['blood_group']) && $_GET['blood_group']=='A-')? 'selected':'' ?>>A-</option>
                            <option <?= (isset($_GET['blood_group']) && $_GET['blood_group']=='B+')? 'selected':'' ?>>B+</option>
                            <option <?= (isset($_GET['blood_group']) && $_GET['blood_group']=='O+')? 'selected':'' ?>>O+</option>
                            <option <?= (isset($_GET['blood_group']) && $_GET['blood_group']=='O-')? 'selected':'' ?>>O-</option>
                            <option <?= (isset($_GET['blood_group']) && $_GET['blood_group']=='AB+')? 'selected':'' ?>>AB+</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="city" required value="<?= htmlspecialchars($_GET['city'] ?? 'Sai') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="search" value="1" class="btn btn-danger w-100 fw-bold">Smart Rank</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if($searched): ?>
        <h5 class="fw-bold mb-3">Top Ranked Available Donors</h5>
        <div class="row g-3">
            <?php foreach($search_results as $index => $donor): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="<?= $index == 0 ? 'border: 2px solid var(--danger) !important; transform: scale(1.02);' : '' ?>">
                    <div class="card-body position-relative">
                        <?php if($index == 0): ?>
                            <span class="position-absolute top-0 end-0 badge bg-danger m-3 pulse">Top Match 🔥</span>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between">
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($donor['name']) ?></h5>
                            <h5 class="<?= $donor['match_score'] > 80 ? 'text-success' : 'text-warning' ?> fw-bold"><?= $donor['match_score'] ?>%</h5>
                        </div>
                        <p class="text-muted small mb-3"><?= $donor['status_icon'] ?> (<?= round($donor['distance_km'], 1) ?> km away)</p>
                        
                        <button class="btn btn-outline-danger w-100 mt-2 blast-btn" onclick="alert('Sent Instant Alert to <?= htmlspecialchars($donor['mobile']) ?>')">
                            Ping Donor
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

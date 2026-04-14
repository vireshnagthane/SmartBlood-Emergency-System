<?php
session_start();
include 'db.php';

if (!isset($_SESSION['donor_id'])) {
    header("Location: donor_login.php");
    exit;
}

$donor_id = $_SESSION['donor_id'];
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $last_donation = !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : NULL;
        
        // Donation Eligibility Logic
        if ($last_donation) {
            $date1 = new DateTime($last_donation);
            $date2 = new DateTime();
            $interval = $date1->diff($date2);
            if ($interval->days < 90) {
                $is_available = 0;
                $msg = "<div class='alert alert-warning'>Status set to 'Not Available'. You cannot donate within 90 days of your last donation.</div>";
            }
        }

        $stmt = $conn->prepare("UPDATE donors SET is_available=?, last_donation_date=? WHERE id=?");
        $stmt->bind_param("isi", $is_available, $last_donation, $donor_id);
        if ($stmt->execute() && empty($msg)) {
            $msg = "<div class='alert alert-success'>Profile updated successfully!</div>";
        }
        $stmt->close();
    } elseif (isset($_POST['add_points'])) {
        $conn->query("UPDATE donors SET donor_points = donor_points + 10 WHERE id = $donor_id");
        $msg = "<div class='alert alert-success'>🎉 +10 Hero Points added!</div>";
    }
}

$stmt = $conn->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Badge Logic
$pts = $donor['donor_points'];
if ($pts >= 150) { $badgeClass = 'badge-hero pulse'; $bName = 'Hero Donor 🦸‍♂️'; }
else if ($pts >= 50) { $badgeClass = 'badge-silver'; $bName = 'Silver Donor 🥈'; }
else { $badgeClass = 'badge-bronze'; $bName = 'Bronze Donor 🥉'; }

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Donor Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">🩸 SmartBlood</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 fw-bold">Welcome, <?= htmlspecialchars($donor['name']) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5 fade-in">
    <div class="row align-items-start g-4">
        <!-- Main Profile -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">My Profile</h4>
                    <?php if($donor['is_available']): ?>
                        <span class="badge-available">Status: Available</span>
                    <?php else: ?>
                        <span class="badge-busy">Status: Not Available</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?= $msg ?>
                    <form method="post">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Date of Last Donation</label>
                                <input type="date" class="form-control" name="last_donation_date" value="<?= $donor['last_donation_date'] ?>">
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="availabilityToggle" name="is_available" <?= $donor['is_available'] ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="availabilityToggle">Available to Donate?</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="update_profile" class="btn btn-primary w-100">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Gamification Badge -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-5">
                    <h5 class="fw-bold mb-3">Hero Status</h5>
                    <div class="badge <?= $badgeClass ?> fs-4 p-3 rounded-pill w-100 mb-3">
                        <?= $bName ?>
                    </div>
                    <h2 class="fw-bold text-danger display-4 mb-0"><?= $pts ?></h2>
                    <p class="text-muted text-uppercase small">Lifetime Points</p>
                    
                    <form method="post" class="mt-4 border-top pt-4">
                        <p class="small text-muted mb-2">Simulate an accepted donation to level up.</p>
                        <button type="submit" name="add_points" class="btn btn-outline-success font-monospace w-100">+10 Simulate Donation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
session_start();
include 'db.php';

if(!isset($_SESSION['hospital_id'])){
    header("Location: login.php");
    exit;
}
$hospital_id = $_SESSION['hospital_id'];
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_request'])) {
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];
    $urgency_level = $_POST['urgency_level'];
    
    $stmt = $conn->prepare("INSERT INTO blood_requests (hospital_id, blood_group, city, urgency_level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $hospital_id, $blood_group, $city, $urgency_level);
    
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>Emergency request created successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

if (isset($_GET['complete_id'])) {
    $complete_id = (int)$_GET['complete_id'];
    $stmt = $conn->prepare("UPDATE blood_requests SET status='Completed' WHERE id=? AND hospital_id=?");
    $stmt->bind_param("ii", $complete_id, $hospital_id);
    $stmt->execute();
    $stmt->close();
    header("Location: request_blood.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Manage Blood Requests</title>
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
    <?= $msg ?>
    <div class="row align-items-start g-4">
        <!-- Create Request Form -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm border-top border-4 border-danger">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold text-danger">Raise Emergency Request</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Blood Group Needed</label>
                            <select class="form-select" name="blood_group" required>
                                <option value="">Select</option>
                                <option>A+</option><option>A-</option>
                                <option>B+</option><option>B-</option>
                                <option>O+</option><option>O-</option>
                                <option>AB+</option><option>AB-</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required placeholder="Target City">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Urgency Level</label>
                            <select class="form-select" name="urgency_level" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High" class="text-danger fw-bold">High (Life-Threatening)</option>
                            </select>
                        </div>
                        <button type="submit" name="create_request" class="btn btn-danger w-100 fw-bold">Broadcast Request</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Active Requests List -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-0 mb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold">My Active Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Blood Group</th>
                                    <th>City</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $reqs = $conn->query("SELECT id, blood_group, city, urgency_level, status FROM blood_requests WHERE hospital_id = $hospital_id ORDER BY status DESC, id DESC");
                                if($reqs->num_rows > 0) {
                                    while($r = $reqs->fetch_assoc()) {
                                        $urgBadge = $r['urgency_level'] == 'High' ? 'bg-danger pulse' : ($r['urgency_level'] == 'Medium' ? 'bg-warning text-dark' : 'bg-info text-dark');
                                        echo "<tr>
                                            <td class='ps-4 fw-bold fs-5'>{$r['blood_group']}</td>
                                            <td>{$r['city']}</td>
                                            <td><span class='badge {$urgBadge}'>{$r['urgency_level']}</span></td>
                                            <td>
                                                ".($r['status'] == 'Pending' ? "<span class='text-warning fw-bold'>Pending</span>" : "<span class='text-success fw-bold'>Completed</span>")."
                                            </td>
                                            <td class='text-end pe-4'>";
                                            if($r['status'] == 'Pending') {
                                                echo "<a href='request_blood.php?complete_id={$r['id']}' class='btn btn-sm btn-success'>Mark Fulfilled</a>";
                                            } else {
                                                echo "<span class='btn btn-sm btn-outline-secondary disabled'>Done</a>";
                                            }
                                        echo "</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted py-4'>No requests raised yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

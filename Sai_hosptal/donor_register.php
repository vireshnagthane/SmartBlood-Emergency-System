<?php
session_start();
include 'db.php';
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_donor'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $city = $_POST['city'];
    $blood_group = $_POST['blood_group'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Check if eligible based on health checkbox
    if (!isset($_POST['health_eligible'])) {
        $msg = "<div class='alert alert-danger'>You must confirm your health eligibility.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO donors (name, mobile, password, city, blood_group, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $mobile, $password, $city, $blood_group, $is_available);
        
        try {
            $stmt->execute();
            $msg = "<div class='alert alert-success'>Registration successful! <a href='donor_login.php'>Login here</a>.</div>";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $msg = "<div class='alert alert-danger'>Mobile number already registered. Please login or use a different number.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Donor Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center py-4" style="min-height: 100vh; background: var(--background);">

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-header border-0 text-center pb-0">
                    <h2 class="fw-bold">Donor Registration</h2>
                    <p class="text-muted">Join the lifesaver community</p>
                </div>
                <div class="card-body">
                    <?= $msg ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" name="mobile" required placeholder="10-digit mobile number" pattern="[0-9]{10}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required placeholder="Secure password">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Blood Group</label>
                                <select class="form-select" name="blood_group" required>
                                    <option value="">Select</option>
                                    <option>A+</option><option>A-</option>
                                    <option>B+</option><option>B-</option>
                                    <option>O+</option><option>O-</option>
                                    <option>AB+</option><option>AB-</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" required placeholder="e.g. Sai">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="availabilityToggle" name="is_available" checked>
                            <label class="form-check-label fw-bold" for="availabilityToggle">I am currently available to donate</label>
                            <div class="form-text text-muted">You can toggle this from your profile later.</div>
                        </div>

                        <div class="mb-4 form-check mt-3 bg-light p-3 rounded border">
                            <input type="checkbox" class="form-check-input ms-1" id="healthCheck" name="health_eligible" required>
                            <label class="form-check-label ms-4" for="healthCheck">
                                I confirm that I am healthy, above 18 years old, and have not donated blood in the last 90 days.
                            </label>
                        </div>
                        
                        <button type="submit" name="register_donor" class="btn btn-primary w-100 py-2 fw-bold">Register as Donor</button>
                    </form>
                    <div class="text-center mt-3">
                        Already registered? <a href="donor_login.php" class="text-decoration-none">Login</a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="signup.php" class="text-muted text-decoration-none">← Back</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

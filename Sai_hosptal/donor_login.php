<?php
session_start();
include 'db.php';
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM donors WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Support both hashed passwords and legacy plain text mock data
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            $_SESSION['donor_id'] = $row['id'];
            $_SESSION['donor_name'] = $row['name'];
            
            // Update last_active_time
            $conn->query("UPDATE donors SET last_active_time=NOW() WHERE id=".$row['id']);
            
            header("Location: donor_profile.php");
            exit;
        } else {
            $msg = "<div class='alert alert-danger'>Invalid Password</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Mobile number not registered</div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Donor Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: var(--background);">

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 border-danger border-top border-4">
                <div class="card-header border-0 text-center pb-0">
                    <h2 class="fw-bold">Donor Login</h2>
                    <p class="text-muted">Manage your availability & profile</p>
                </div>
                <div class="card-body">
                    <?= $msg ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" name="mobile" required placeholder="10-digit mobile number">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required placeholder="••••••••">
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold">Login to Profile</button>
                    </form>
                    <div class="text-center mt-4">
                        New donor? <a href="donor_register.php" class="text-decoration-none">Register here</a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="index.php" class="text-muted text-decoration-none">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

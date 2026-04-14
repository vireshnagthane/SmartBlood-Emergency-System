<?php
session_start();
include 'db.php';
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_hospital'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password_hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $city = $_POST['city'];
    $address = $_POST['address'];
    
    $stmt = $conn->prepare("INSERT INTO hospitals (name, username, password, city, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $username, $password_hashed, $city, $address);
    
    try {
        $stmt->execute();
        $msg = "<div class='alert alert-success'>Hospital Registered Successfully! <a href='login.php'>Login here</a>.</div>";
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $msg = "<div class='alert alert-danger'>Username already exists. Please choose a different username.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Hospital Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center py-4" style="min-height: 100vh; background: var(--background);">

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 border-danger border-top border-4">
                <div class="card-header border-0 text-center pb-0">
                    <h2 class="fw-bold">Hospital/Bank Registration</h2>
                    <p class="text-muted">Register to find donors instantly</p>
                </div>
                <div class="card-body">
                    <?= $msg ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Hospital Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="General Hospital">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required placeholder="admin123">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required placeholder="Secure password">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required placeholder="e.g. Sai">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Full Address</label>
                            <textarea class="form-control" name="address" rows="3" required placeholder="Street, Sector..."></textarea>
                        </div>
                        
                        <button type="submit" name="register_hospital" class="btn btn-danger w-100 py-2 fw-bold">Register Hospital</button>
                    </form>
                    <div class="text-center mt-3">
                        Already registered? <a href="login.php" class="text-decoration-none">Hospital Login</a>
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

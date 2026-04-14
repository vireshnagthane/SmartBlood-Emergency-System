<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Smart Blood System</title>
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: var(--background);">

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center mb-4">
            <h1 class="fw-bold mb-3">Join the Platform</h1>
            <p class="text-muted">Choose your account type to proceed with registration.</p>
        </div>
    </div>
    
    <div class="row justify-content-center g-4">
        <div class="col-md-5">
            <div class="card h-100 text-center p-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div style="font-size: 4rem; margin-bottom: 20px;">👤</div>
                    <h3 class="card-title fw-bold">Blood Donor</h3>
                    <p class="card-text text-muted mb-4">Register to donate blood, track your donations, and receive emergency alerts.</p>
                    <a href="donor_register.php" class="btn btn-primary btn-lg w-100 mt-auto">Donor Signup</a>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100 text-center p-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🏥</div>
                    <h3 class="card-title fw-bold">Hospital/Blood Bank</h3>
                    <p class="card-text text-muted mb-4">Create an account to search for donors during emergencies and manage requests.</p>
                    <a href="hospital_register.php" class="btn btn-outline-primary btn-lg w-100 mt-auto">Hospital Signup</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="index.php" class="text-decoration-none text-muted">← Back to Home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

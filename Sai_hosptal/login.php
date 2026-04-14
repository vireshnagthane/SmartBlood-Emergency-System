<?php
session_start();
include 'db.php';
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM hospitals WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Support both hashed passwords and legacy plain text mock data
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            $_SESSION['hospital_id'] = $row['id'];
            $_SESSION['hospital_name'] = $row['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $msg = "<div class='alert alert-danger'>Invalid Password</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Invalid Username</div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Hospital Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePassword(id) {
            var x = document.getElementById(id);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: var(--background);">

<div class="container fade-in">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 border-danger border-top border-4">
                <div class="card-header border-0 text-center pb-0">
                    <h2 class="fw-bold">Hospital Login</h2>
                    <p class="text-muted">Access your admin dashboard</p>
                </div>
                <div class="card-body">
                    <?= $msg ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required placeholder="admin123">
                        </div>
                        <div class="mb-4">
                            <label class="form-label d-flex justify-content-between">
                                Password
                                <a href="#" class="text-decoration-none text-muted" onclick="togglePassword('loginPass')" style="font-size:0.85rem">Show/Hide</a>
                            </label>
                            <input type="password" class="form-control" name="password" id="loginPass" required placeholder="••••••••">
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-danger w-100 py-2 fw-bold">Login to Dashboard</button>
                    </form>
                    <div class="text-center mt-4">
                        New hospital? <a href="hospital_register.php" class="text-decoration-none">Register here</a>
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

<?php
include 'db_connect.php'; 

$message = "";

if (isset($_POST['register'])) {
    // Sanitize inputs for security
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    // 1. CHECK if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-warning border-0 shadow-sm'>⚠️ This email is already registered. <a href='login.php' class='fw-bold'>Login here</a>.</div>";
    } else {
        // 2. Insert new user
        $sql = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$password')";

        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id;
            
            // 3. Automatically create bank account with 0.00 balance
            $sql_account = "INSERT INTO accounts (user_id, balance) VALUES ('$user_id', '0.00')";
            $conn->query($sql_account);
            
            $message = "<div class='alert alert-success border-0 shadow-sm'>✅ Account created successfully! <a href='login.php' class='fw-bold text-success'>Login now</a></div>";
        } else {
            $message = "<div class='alert alert-danger border-0 shadow-sm'>❌ Registration Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join MyBank - Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body { background: #f0f2f5; font-family: 'Inter', sans-serif; }
        .signup-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: 20px; overflow: hidden; }
        .btn-primary { background: var(--primary-gradient); border: none; border-radius: 12px; padding: 12px; font-weight: 600; transition: 0.3s; }
        .btn-primary:hover { transform: scale(1.02); opacity: 0.9; }
        .form-control { border-radius: 10px; padding: 12px; background: #f8f9fa; border: 1px solid #e9ecef; }
        .form-control:focus { box-shadow: none; border-color: #667eea; }
        .brand-text { background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; }
    </style>
</head>
<body>

    <div class="container signup-container">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-5 col-md-8">
                
                <div class="text-center mb-4">
                    <h2 class="brand-text">🏦 MyBank</h2>
                    <p class="text-muted">Start your digital banking journey today</p>
                </div>

                <?php echo $message; ?>

                <div class="card shadow-lg p-4">
                    <div class="card-body">
                        <form method="POST" action="signup.php">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Rabia Irshad" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="03XXXXXXXXX" required pattern="[0-9]{11}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required minlength="6">
                                </div>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary w-100 mt-3 shadow">Get Started</button>
                        </form>
                        <div class="text-center mt-4">
                            <span class="text-muted small">By registering, you agree to our Terms.</span>
                            <hr class="my-4">
                            <p class="mb-0">Already a member? <a href="login.php" class="text-decoration-none fw-bold">Login</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted small">
                    &copy; 2026 MyBank Digital. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
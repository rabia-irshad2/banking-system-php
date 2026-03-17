<?php
include 'db_connect.php';
session_start(); // Starts the session to keep the user logged in

$message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php"); // Redirect to dashboard
        } else {
            $message = "<div class='alert alert-danger'>Invalid password!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>No user found with this email!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Banking App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <?php echo $message; ?>
                <div class="card shadow p-3">
                    <h3 class="text-center">Login</h3>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-success w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="signup.php">Don't have an account? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
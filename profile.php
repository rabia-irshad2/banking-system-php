<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - MyBank Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --sidebar-bg: #1e1e2d; --main-bg: #f4f7fe; --card-bg: #ffffff; --text-color: #1e1e2d; --accent: #4361ee; }
        body.dark-mode { --main-bg: #151521; --card-bg: #1e1e2d; --text-color: #ffffff; --sidebar-bg: #0f0f1a; }
        body { background-color: var(--main-bg); color: var(--text-color); font-family: 'Inter', sans-serif; transition: 0.3s; }
        .wrapper { display: flex; height: 100vh; }
        .sidebar { width: 280px; background: var(--sidebar-bg); color: white; padding: 30px 20px; display: flex; flex-direction: column; }
        .nav-link { color: #a2a3b7; margin-bottom: 8px; border-radius: 12px; padding: 12px 20px; text-decoration: none; display: flex; align-items: center; }
        .nav-link.active { background: rgba(67, 97, 238, 0.15); color: var(--accent); font-weight: 600; }
        .profile-card { background: var(--card-bg); border-radius: 25px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .form-control { background-color: var(--main-bg); color: var(--text-color); border: 1px solid rgba(255,255,255,0.1); }
        .form-control:focus { background-color: var(--main-bg); color: var(--text-color); }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h4 class="fw-bold mb-5 px-3">🏦 MyBank</h4>
        <nav class="nav flex-column flex-grow-1">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-3"></i> Dashboard</a>
            <a class="nav-link" href="transactions.php"><i class="bi bi-arrow-left-right me-3"></i> History</a>
            <a class="nav-link active" href="profile.php"><i class="bi bi-person-circle me-3"></i> Profile</a>
        </nav>
        <a class="nav-link text-danger" href="logout.php"><i class="bi bi-power me-3"></i> Log Out</a>
    </div>
    <div class="main-content p-5 w-100 overflow-auto">
        <div class="profile-card">
            <h3 class="fw-bold mb-4">Account Settings</h3>
            <form action="update_profile.php" method="POST">
                <label class="form-label small fw-bold opacity-75">Full Name</label>
                <input type="text" name="name" class="form-control mb-3" value="<?php echo $user['name']; ?>">
                <label class="form-label small fw-bold opacity-75">Email Address</label>
                <input type="email" class="form-control mb-4" value="<?php echo $user['email']; ?>" readonly>
                <button type="submit" class="btn btn-primary px-5 rounded-pill">Save Changes</button>
            </form>
        </div>
    </div>
</div>
<script>if(localStorage.getItem('theme') === 'dark') document.body.classList.add('dark-mode');</script>
</body>
</html>
<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transactions - MyBank Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { 
            --sidebar-bg: #1e1e2d; 
            --main-bg: #f4f7fe; 
            --card-bg: #ffffff; 
            --text-color: #1e1e2d; 
            --accent: #4361ee;
            --table-text: #1e1e2d;
        }
        body.dark-mode { 
            --main-bg: #151521; 
            --card-bg: #1e1e2d; 
            --text-color: #ffffff; 
            --sidebar-bg: #0f0f1a;
            --table-text: #ffffff;
        }
        
        body { 
            background-color: var(--main-bg); 
            color: var(--text-color); 
            transition: background 0.3s ease; 
            font-family: 'Inter', sans-serif;
        }
        
        .wrapper { display: flex; height: 100vh; }
        .sidebar { width: 280px; background: var(--sidebar-bg); color: white; padding: 30px 20px; }
        .nav-link { color: #a2a3b7; padding: 12px 20px; text-decoration: none; display: flex; align-items: center; border-radius: 12px; }
        .nav-link.active { background: rgba(67, 97, 238, 0.15); color: var(--accent); font-weight: 600; }

        .table-card { 
            background: var(--card-bg); 
            border-radius: 25px; 
            padding: 30px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* --- Table Text Visibility Fix --- */
        .table { 
            color: var(--table-text) !important; 
            --bs-table-bg: transparent;
            --bs-table-border-color: rgba(255,255,255,0.1);
        }

        .table thead th {
            background-color: rgba(67, 97, 238, 0.15);
            color: var(--table-text) !important;
            border: none;
            padding: 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: var(--table-text);
        }

        /* Row hover for better readability */
        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .type-badge {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h4 class="fw-bold mb-5 px-3">🏦 MyBank</h4>
        <nav class="nav flex-column flex-grow-1">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-3"></i> Dashboard</a>
            <a class="nav-link active" href="transactions.php"><i class="bi bi-arrow-left-right me-3"></i> History</a>
            <a class="nav-link" href="profile.php"><i class="bi bi-person-circle me-3"></i> Profile</a>
        </nav>
        <a class="nav-link text-danger mt-auto" href="logout.php"><i class="bi bi-power me-3"></i> Log Out</a>
    </div>

    <div class="p-5 w-100 overflow-auto">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Full Transaction History</h3>
                <a href="dashboard.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">Back to Dashboard</a>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="rounded-start">Date & Time</th>
                            <th>Type</th>
                            <th class="rounded-end text-end">Amount (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="color: inherit; opacity: 0.8;">
                                <?php echo date('d M Y, H:i', strtotime($row['date'])); ?>
                            </td>
                            <td>
                                <span class="type-badge <?php echo $row['type'] == 'deposit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
                                    <?php echo strtoupper($row['type']); ?>
                                </span>
                            </td>
                            <td class="fw-bold text-end <?php echo $row['type'] == 'deposit' ? 'text-success' : 'text-danger'; ?>">
                                <?php echo ($row['type'] == 'deposit' ? '+' : '-'); ?> Rs <?php echo number_format($row['amount'], 2); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    if(localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>
</body>
</html>
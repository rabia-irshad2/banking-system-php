<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch current balance
$query = "SELECT balance FROM accounts WHERE user_id = '$user_id'";
$result = $conn->query($query);
$account = $result->fetch_assoc();
$balance = $account['balance'] ?? 0.00;

// Fetch recent transaction history
$history_query = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY date DESC LIMIT 8";
$history_result = $conn->query($history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBank Pro - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { 
            --sidebar-bg: #1e1e2d; 
            --main-bg: #f4f7fe; 
            --card-bg: #ffffff;
            --text-color: #1e1e2d;
            --accent: #4361ee; 
            --border-color: #f8f9fa;
        }

        body.dark-mode {
            --main-bg: #151521; 
            --card-bg: #1e1e2d; 
            --text-color: #ffffff;
            --sidebar-bg: #0f0f1a;
            --border-color: rgba(255,255,255,0.05);
        }

        body { 
            background-color: var(--main-bg); 
            color: var(--text-color);
            font-family: 'Inter', sans-serif; 
            transition: 0.3s; 
        }
        
        .wrapper { display: flex; height: 100vh; }
        .sidebar { width: 280px; background: var(--sidebar-bg); color: white; padding: 30px 20px; display: flex; flex-direction: column; }
        .nav-link { color: #a2a3b7; margin-bottom: 8px; border-radius: 12px; padding: 12px 20px; text-decoration: none; display: flex; align-items: center; }
        .nav-link.active { background: rgba(67, 97, 238, 0.15); color: var(--accent); font-weight: 600; }

        .main-container { flex: 1; display: flex; overflow-y: auto; padding: 30px; }
        .right-panel { flex: 1; background: var(--card-bg); border-radius: 30px; padding: 25px; border: 1px solid var(--border-color); }

        .stat-card { border: 1px solid var(--border-color); border-radius: 20px; padding: 20px; background: var(--card-bg); text-align: center; cursor: pointer; }
        .balance-hero { background: linear-gradient(135deg, #4361ee 0%, #1e1e2d 100%); color: white; border-radius: 25px; padding: 30px; margin-bottom: 30px; }
        
        /* Modal Styling for Dark Mode */
        .modal-content { background-color: var(--card-bg); color: var(--text-color); border: 1px solid var(--border-color); }
        .form-control { background-color: var(--main-bg); color: var(--text-color); border: 1px solid var(--border-color); }
        .form-control:focus { background-color: var(--main-bg); color: var(--text-color); }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h4 class="fw-bold mb-5 px-3">🏦 MyBank</h4>
        <nav class="nav flex-column flex-grow-1">
            <a class="nav-link active" href="dashboard.php"><i class="bi bi-grid-fill me-3"></i> Dashboard</a>
            <a class="nav-link" href="transactions.php"><i class="bi bi-arrow-left-right me-3"></i> History</a>
            <a class="nav-link" href="profile.php"><i class="bi bi-person-circle me-3"></i> Profile</a>
        </nav>

        <div class="mt-auto p-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="small text-muted ms-2" for="darkModeToggle">Dark Mode</label>
            </div>
        </div>
        <a class="nav-link text-danger" href="logout.php"><i class="bi bi-power me-3"></i> Log Out</a>
    </div>

    <div class="main-container">
        <div class="col-8 pe-4">
            <h3 class="fw-bold mb-4">Overview</h3>
            <div class="balance-hero shadow">
                <h6 class="opacity-75 small fw-bold">CURRENT BALANCE</h6>
                <h1 class="display-5 fw-bold mb-0">Rs <?php echo number_format($balance, 2); ?></h1>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card shadow-sm" data-bs-toggle="modal" data-bs-target="#depositModal">
                        <i class="bi bi-plus-circle text-primary fs-3"></i>
                        <h6 class="mt-2 mb-0">Deposit</h6>
                        <button class="btn btn-sm btn-primary w-100 rounded-pill mt-2">Add Money</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="bi bi-dash-circle text-warning fs-3"></i>
                        <h6 class="mt-2 mb-0">Withdraw</h6>
                        <button class="btn btn-sm btn-warning w-100 rounded-pill mt-2">Cash Out</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                        <i class="bi bi-send text-info fs-3"></i>
                        <h6 class="mt-2 mb-0">Transfer</h6>
                        <button class="btn btn-sm btn-info text-white w-100 rounded-pill mt-2">Send Funds</button>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-4 shadow-sm" style="background-color: var(--card-bg) !important;">
                <h6 class="fw-bold mb-4">Balance Growth</h6>
                <canvas id="balanceChart" height="150"></canvas>
            </div>
        </div>

        <div class="right-panel shadow-sm">
            <h6 class="fw-bold mb-4">Transactions</h6>
            <div class="history-list">
                <?php while($row = $history_result->fetch_assoc()): ?>
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom border-light">
                        <div class="flex-grow-1">
                            <p class="mb-0 small fw-bold"><?php echo ucfirst($row['type']); ?></p>
                            <small class="text-muted" style="font-size: 10px;"><?php echo date('d M', strtotime($row['date'])); ?></small>
                        </div>
                        <span class="fw-bold <?php echo $row['type'] == 'deposit' ? 'text-success' : 'text-danger'; ?>">
                            Rs <?php echo number_format($row['amount'], 0); ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 border-0 rounded-4 shadow">
            <form action="deposit.php" method="POST">
                <h5 class="fw-bold mb-3">Deposit Money</h5>
                <input type="number" name="amount" class="form-control form-control-lg rounded-3 mb-3" placeholder="Enter Amount" required>
                <button type="submit" name="deposit_btn" class="btn btn-primary w-100 btn-lg rounded-pill">Confirm Deposit</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 border-0 rounded-4 shadow">
            <form action="withdraw.php" method="POST">
                <h5 class="fw-bold mb-3">Withdraw Cash</h5>
                <input type="number" name="amount" class="form-control form-control-lg rounded-3 mb-3" placeholder="Enter Amount" required>
                <button type="submit" name="withdraw_btn" class="btn btn-warning w-100 btn-lg rounded-pill">Confirm Withdrawal</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4 border-0 rounded-4 shadow">
            <form action="transfer.php" method="POST">
                <h5 class="fw-bold mb-3">Send Money</h5>
                <input type="email" name="receiver_email" class="form-control rounded-3 mb-2" placeholder="Receiver Email" required>
                <input type="number" name="amount" class="form-control rounded-3 mb-3" placeholder="Amount (Rs)" required>
                <button type="submit" name="transfer_btn" class="btn btn-info text-white w-100 btn-lg rounded-pill">Transfer Now</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Theme Logic
    const toggle = document.getElementById('darkModeToggle');
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        toggle.checked = true;
    }
    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });

    // Chart
    const ctx = document.getElementById('balanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            datasets: [{
                label: 'Activity',
                data: [5000, 8000, 7000, 9000, <?php echo $balance; ?>, 0, 0],
                backgroundColor: '#4361ee',
                borderRadius: 8,
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
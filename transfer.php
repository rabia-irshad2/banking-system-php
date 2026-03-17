<?php
include 'db_connect.php';
session_start();

if (isset($_POST['transfer_btn'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_email = mysqli_real_escape_string($conn, $_POST['receiver_email']);
    $amount = $_POST['amount'];

    // 1. Find the receiver's user_id
    $user_check = "SELECT id FROM users WHERE email = '$receiver_email'";
    $user_res = $conn->query($user_check);

    if ($user_res->num_rows > 0) {
        $receiver_data = $user_res->fetch_assoc();
        $receiver_id = $receiver_data['id'];

        // Prevent sending money to yourself
        if ($sender_id == $receiver_id) {
            header("Location: dashboard.php?error=self_transfer");
            exit();
        }

        // 2. Check sender's balance
        $balance_check = "SELECT balance FROM accounts WHERE user_id = '$sender_id'";
        $bal_res = $conn->query($balance_check);
        $sender_bal = $bal_res->fetch_assoc()['balance'];

        if ($sender_bal >= $amount) {
            // START TRANSACTION (Keeps data safe if one part fails)
            $conn->begin_transaction();

            try {
                // 3. Update Balances
                $conn->query("UPDATE accounts SET balance = balance - $amount WHERE user_id = '$sender_id'");
                $conn->query("UPDATE accounts SET balance = balance + $amount WHERE user_id = '$receiver_id'");

                // 4. Record Transactions
                // Record for Sender (Withdrawal type)
                $conn->query("INSERT INTO transactions (user_id, amount, type, date) VALUES ('$sender_id', '$amount', 'transfer_out', NOW())");
                
                // Record for Receiver (Deposit type)
                $conn->query("INSERT INTO transactions (user_id, amount, type, date) VALUES ('$receiver_id', '$amount', 'transfer_in', NOW())");

                $conn->commit();
                header("Location: dashboard.php?status=transfer_success");
            } catch (Exception $e) {
                $conn->rollback();
                header("Location: dashboard.php?error=system_error");
            }
        } else {
            header("Location: dashboard.php?error=insufficient_funds");
        }
    } else {
        header("Location: dashboard.php?error=user_not_found");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History - MyBank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">⬅ Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Your Transaction History</h4>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['date']; ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo ($row['type'] == 'deposit') ? 'bg-success' : 
                                                 (($row['type'] == 'withdraw') ? 'bg-danger' : 'bg-info'); 
                                        ?> text-uppercase">
                                            <?php echo $row['type']; ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold">
                                        <?php echo ($row['type'] == 'deposit') ? '+' : '-'; ?> 
                                        Rs <?php echo number_format($row['amount'], 2); ?>
                                    </td>
                                    <td>
                                        <?php echo (!empty($row['with_to'])) ? "To: " . $row['with_to'] : "-"; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php
include 'db_connect.php';
session_start();

if (isset($_POST['withdraw_btn'])) {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];

    // Check current balance first
    $query = "SELECT balance FROM accounts WHERE user_id = '$user_id'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($row['balance'] >= $amount) {
        // Deduct from balance
        $update_query = "UPDATE accounts SET balance = balance - $amount WHERE user_id = '$user_id'";
        $conn->query($update_query);

        // Record the transaction
        $insert_query = "INSERT INTO transactions (user_id, amount, type, date) VALUES ('$user_id', '$amount', 'withdraw', NOW())";
        $conn->query($insert_query);

        header("Location: dashboard.php?status=success");
    } else {
        header("Location: dashboard.php?status=insufficient_funds");
    }
}
?>
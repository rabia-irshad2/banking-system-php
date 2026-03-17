<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['deposit_btn'])) {
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    if ($amount > 0) {
        // 1. Update the balance in the accounts table
        $update_sql = "UPDATE accounts SET balance = balance + $amount WHERE user_id = '$user_id'";
        
        if ($conn->query($update_sql) === TRUE) {
            // 2. Record the transaction in the transactions table
            $insert_log = "INSERT INTO transactions (account_id, type, amount, date) 
                           VALUES ((SELECT id FROM accounts WHERE user_id = '$user_id'), 'deposit', '$amount', NOW())";
            $conn->query($insert_log);

            header("Location: dashboard.php?success=deposited");
        }
    }
}
?>
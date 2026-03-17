<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_image'];
    
    // File properties
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Create unique name to prevent overwriting
    $newFileName = "user_" . $user_id . "_" . time() . "." . $fileExt;
    $fileDestination = 'uploads/' . $newFileName;

    // Check if 'uploads' folder exists, if not create it
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (move_uploaded_file($fileTmpName, $fileDestination)) {
        // Update database (assumes your users table has a 'profile_pic' column)
        $query = "UPDATE users SET profile_pic = '$newFileName' WHERE id = '$user_id'";
        if ($conn->query($query)) {
            header("Location: profile.php?success=uploaded");
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
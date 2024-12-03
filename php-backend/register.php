<?php
include "connect.php";

session_start(); // Start session for setting session variables

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$prefer = $_POST['prefer'];
$username = $_POST['username'];
$password = $_POST['pw'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL query to check if username exists
$sql_check_user = "SELECT User_ID FROM User_table WHERE User_username = ?";
$stmt_check_user = mysqli_prepare($conn, $sql_check_user);
mysqli_stmt_bind_param($stmt_check_user, "s", $username);
mysqli_stmt_execute($stmt_check_user);
$result_check_user = mysqli_stmt_get_result($stmt_check_user);

// Generate User_ID
$get_last_ID_sql = "SELECT User_ID FROM User_table ORDER BY User_ID DESC LIMIT 1;";
$last_ID = mysqli_query($conn, $get_last_ID_sql);
$row = mysqli_fetch_assoc($last_ID);
$new_user_ID = $row['User_ID'] + 1;

if (mysqli_num_rows($result_check_user) < 1) {
    // Prepare SQL query to insert the new user
    $sql_insert_user = "INSERT INTO User_table (User_ID, User_fullname, User_username, User_password, User_email, User_settings) 
                        VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
    mysqli_stmt_bind_param($stmt_insert_user, "isssss", $new_user_ID, $fullname, $username, $hashedPassword, $email, $prefer);
    $result_insert_user = mysqli_stmt_execute($stmt_insert_user);
    
    if ($result_insert_user) {
        $_SESSION['User_ID'] = $new_user_ID;
        header("Location: ../web/LeagueSelectPage.php");
        exit();
    } else {
        echo "Error: Could not register the user.";
    }
} else {
    echo "Username already exists!";
}

mysqli_stmt_close($stmt_check_user);
mysqli_stmt_close($stmt_insert_user);
mysqli_close($conn);
?>

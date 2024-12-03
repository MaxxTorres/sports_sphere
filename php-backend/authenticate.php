<?php
include "connect.php";

session_start();

$un = $_POST['uname'];
$pass = $_POST['pwd'];

// Prepare SQL query to fetch user by username
$sql = "SELECT * FROM User_table WHERE User_username = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "s", $un); // Bind username parameter
mysqli_stmt_execute($stmt); // Execute statement

$result = mysqli_stmt_get_result($stmt);

// Check if a matching user was found
if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    // Verify the password hash
    if (password_verify($pass, $row['User_password'])) {
        $_SESSION['User_ID'] = $row['User_ID']; // Store the user ID in session
        $_SESSION['Username'] = $row['User_username'];
        header("Location: ../web/LeagueSelectPage.php"); // Redirect to the league select page
        exit(); 
    } 
    elseif ($row['User_username'] === $un && $row['User_password'] === $pass) { // *So old passwords can work*
        $_SESSION['User_ID'] = $row['User_ID'];
        $_SESSION['Username'] = $row['User_username'];
        header("Location: ../web/LeagueSelectPage.php");
        exit();
    }
    else {
        echo "Incorrect username or password";
    }
} else {
    echo "Incorrect username or password";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<?php
include "connect.php";

session_start();

$un = $_POST['uname'];
$pass = $_POST['pwd'];

// Prepare SQL query
$sql = "SELECT * FROM User_table WHERE User_username = ? AND User_password = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ss", $un, $pass); //Bind parameters
mysqli_stmt_execute($stmt); // Execute statement

$result = mysqli_stmt_get_result($stmt);

// Check if a matching user was found
if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    // Verify username and password
    if ($row['User_username'] === $un && $row['User_password'] === $pass) {
        $_SESSION['User_ID'] = $row['User_ID']; // Store the user ID in session
        $_SESSION['Username'] = $row['User_username'];
        header("Location: ../web/LeagueSelectPage.php"); // Redirect to the league select page
        exit();
    } else {
        echo "Incorrect username or password";
    }
} else {
    echo "Incorrect username or password";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
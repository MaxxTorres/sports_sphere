<?php
    $servname = "54.80.33.178";
    $username = "remote_admin";
    $password = "12345";
    $dbname = "fantasy_sports";
    
    // $servname = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "fantasy_sports";

    $conn = mysqli_connect($servname, $username, $password, $dbname);

    if(!$conn){
        echo "Connection failed: " . mysqli_connect_error();
    } else {
        // echo "Connection successful!";
    }
?>


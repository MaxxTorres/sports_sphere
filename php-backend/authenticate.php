<?php
    include "connect.php";

    session_start();

    $un = $_POST['uname'];
    $pass = $_POST['pwd'];
    $sql = "SELECT * FROM User_table WHERE User_username LIKE '$un' and User_password LIKE '$pass'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result)===1){
        $row = mysqli_fetch_assoc($result);

        if($row['User_username'] === $un && $row['User_password'] === $pass){
            $_SESSION['User_ID'] = $row['User_ID'];
            header("Location: ../web/LeagueSelectPage.php");
        }
        else{
            echo "incorrect username or password";
        }
    }
    else {
        echo "incorrect username or password";
    }
    exit();
?>
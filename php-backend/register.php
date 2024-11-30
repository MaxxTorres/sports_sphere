<?php
    include "connect.php";

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $prefer = $_POST['prefer'];
    $username = $_POST['username'];
    $password = $_POST['pw'];

    $check_user_sql = "SELECT User_ID
                FROM User_table
                WHERE User_username = '$username';";
    $check_user= mysqli_query($conn, $check_user_sql);

    //Generate User_ID
    $get_last_ID_sql = "SELECT User_ID
                        FROM User_table
                        ORDER BY User_ID DESC
                        LIMIT 1; ";
    $last_ID = mysqli_query($conn, $get_last_ID_sql);
    $row = mysqli_fetch_assoc($last_ID);
    $new_user_ID = $row['User_ID'] + 1;

    if (mysqli_num_rows($check_user) < 1){
        $insert_user_sql = "INSERT INTO User_table(User_ID, User_fullname, User_username, User_password, User_email, User_settings)
                            VALUES('$new_user_ID','$fullname','$username','$password','$email','$prefer');";
        $inser_user = mysqli_query($conn, $insert_user_sql);
        header("Location: ../web/LeagueSelectPage.php");            
    } else {
        echo "username already exists!";
    }

    exit();
?>

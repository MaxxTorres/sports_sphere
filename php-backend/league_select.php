<?php
    include "connect.php";

    session_start();

    $league_name = $_GET['league_name'];
    
    $sql = "SELECT * FROM Leagues WHERE League_name = '$league_name'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result)===1){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['League_ID'] = $row['League_ID'];
        $_SESSION['League_name'] = $row['League_name'];
        header("Location: ../web/LeagueStandingsPage.php");
        exit();
    }
    else {
        echo('Invalid league!');
    }
?>
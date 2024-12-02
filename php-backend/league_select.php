<?php
    include "connect.php";

    session_start();

    $league_name = $_GET['league_name'];
    
    $sql = "SELECT * FROM Leagues WHERE League_name = '$league_name'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result)===1){
        $league_row = mysqli_fetch_assoc($result);
        $_SESSION['League_ID'] = $league_row['League_ID'];
        $_SESSION['League_name'] = $league_row['League_name'];

        $commissioner_ID_sql = "SELECT League_commissioner
                                FROM Leagues
                                WHERE League_ID = '" . $_SESSION['League_ID'] . "' ";
        $commissioner_ID = mysqli_query($conn, $commissioner_ID_sql);
        $commissioner_row = mysqli_fetch_assoc($commissioner_ID);
        
        $_SESSION['test'] = $commissioner_row['League_commissioner'];

        if($_SESSION['User_ID'] == $commissioner_row['League_commissioner']) {
            $_SESSION['role'] = "Commissioner";
        } else {
            $_SESSION['role'] = "Player";
        }

        header("Location: ../web/LeagueStandingsPage.php");
        exit();
    }
    else {
        echo('Invalid league!');
    }

    mysqli_close($conn);
?>
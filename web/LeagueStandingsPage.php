<?php
include "connect.php";
session_start();

$query = "SELECT Player_name, Player_position, Player_fantasy_points
            FROM Teams t
            INNER JOIN User_table u ON u.User_ID = t.User_ID
            INNER JOIN Players p ON p.Team_ID = t.Team_ID
            WHERE u.User_ID = '$_SESSION['user_id']' and t.League_ID = '$_SESSION['league_id']';";

$result = mysqli_query($conn, $query);
$players = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <div class = "general_container" style = "margin-left: 200px;"> 
        League Standings
    </div>



    <!-- SIDEBAR -->
    <div id = "side_bar">
        <?php
            if (isset($_SESSION['League_name'])) {
                echo "<p>" . htmlspecialchars($_SESSION['League_name']) . "</p>";
            } else {
                echo "<p>No league selected.</p>";
            }
        ?>
        <div style = "margin: 5px; margin-top: 50px; margin-left: 10px">
        <a class = "side_bar_button" href = "LeagueSelectPage.html">League Select</a>
        <a class = "side_bar_button" href = "LeagueStandingsPage.php">League Standings</a>
        <a class = "side_bar_button" href = "TeamPage.html">Your Team</a>
        <a class = "side_bar_button" href = "MatchesPage.html">Matches</a>
        <a class = "side_bar_button" href = "DraftPage.html">Draft</a>
        <a class = "side_bar_button" href = "PlayersPage.html">Players</a>
        <a class = "side_bar_button" href = "TradePage.html">Trades</a>
        </div>
    </div>
    
</body>
</html>
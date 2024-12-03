<?php
include "../php-backend/connect.php";
session_start();

$query = "SELECT Player_ID, Player_name, Player_position, Player_fantasy_points
            FROM Teams t
            INNER JOIN User_table u ON u.User_ID = t.User_ID
            INNER JOIN Players p ON p.Team_ID = t.Team_ID
            WHERE u.User_ID = '" . $_SESSION['User_ID'] . "' 
            AND t.League_ID = '" . $_SESSION['League_ID'] . "';";

$result = mysqli_query($conn, $query);
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
    <div class = "general_container" style = "position: relative; margin-left: 200px; margin-top: 100px"> 
        <div class = "container_header">
            Your Team
        </div>
        <?php
            if ($result && mysqli_num_rows($result) > 0) {
                // Start the HTML table
                echo "<table class = 'general_table'>
                        <thead>
                            <tr>
                                <th>Player Name</th>
                                <th>Player Position</th>
                                <th>Fantasy Points</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>";
            
                // Loop through each row in the result set
                while ($players = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($players['Player_name']) . "</td>
                            <td>" . htmlspecialchars($players['Player_position']) . "</td>
                            <td>" . htmlspecialchars($players['Player_fantasy_points']) . "</td>
                            <td> 
                                <form method='POST' action='../php-backend/remove_player.php' style='display:inline;'>
                                    <input id='p_id' name='p_id' value='" . $players['Player_ID'] . "' type='hidden'>
                                    <button type='submit' onclick='return confirm(\"Are you sure you want to remove this player?\")' class='button'>Unsign</button>
                                </form>
                            </td>
                          </tr>";
                }
            
                // Close the table
                echo "</tbody>
                    </table>";
            } else {
                // If no results are returned
                echo "<p>No players found.</p>";
            }
        ?>
    </div>

    
    <!-- SIDEBAR -->
    <div id = "side_bar">
        <?php
            if (isset($_SESSION['League_name'])) {
                echo "<h1>" . htmlspecialchars($_SESSION['League_name']) . "</h1>";
            } else {
                echo "<h1>No league selected.</h1>";
            }
            echo "<p>" . htmlspecialchars($_SESSION['Username']), " (", htmlspecialchars($_SESSION['role']), ")" . "</p>";
        ?>
        <div style = "margin: 5px; margin-left: 20px; font-size: 14px;">
            <a style = "padding: 5px; color: lightgrey; text-decoration: underline;" class = "side_bar_button" href = "../php-backend/logout.php">Log out</a>
            <a style = "padding: 5px; color: lightgrey; text-decoration: underline;" class = "side_bar_button" href = "LeagueSelectPage.php">League Select</a>
            <a style = "padding: 5px; color: lightgrey; text-decoration: underline;" class = "side_bar_button" href = "settings.php">Settings</a>
        </div>
        <div style = "margin: 5px; margin-left: 10px; margin-top: 20px;">
            <a class = "side_bar_button" href = "LeagueStandingsPage.php">| League Standings</a>
            <a class = "side_bar_button" href = "TeamPage.php">| Your Team</a>
            <a class = "side_bar_button" href = "MatchesPage.php">| Matches</a>
            <a class = "side_bar_button" href = "DraftPage.php">| Draft</a>
            <a class = "side_bar_button" href = "PlayersPage.php">| Players</a>
            <a class = "side_bar_button" href = "TradePage.php">| Trades</a>
           
        </div>
        <?php
            if (isset($_SESSION['League_name'])) {
                if ($_SESSION['League_name'] == "NBA League") {
                    echo "<div style='text-align: center;'> <img src = './images/nba_logo.png'> </div>";
                } elseif ($_SESSION['League_name'] == "MLS League") {
                    echo "<div style='text-align: center;'> <img src = './images/mls_logo.png'> </div>";
                } elseif ($_SESSION['League_name'] == "NFL League") {
                    echo "<div style='text-align: center;'> <img src = './images/nfl_logo.png'> </div>";
                }
            } else {
                echo "<p>No league selected.</p>";
            }
        ?>
    </div>

</body>
</html>
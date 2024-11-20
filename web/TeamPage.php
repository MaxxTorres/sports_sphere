<?php
include "../php-backend/connect.php";
session_start();

$query = "SELECT Player_name, Player_position, Player_fantasy_points
            FROM Teams t
            INNER JOIN User_table u ON u.User_ID = t.User_ID
            INNER JOIN Players p ON p.Team_ID = t.Team_ID
            WHERE u.User_ID = '" . $_SESSION['User_ID'] . "' 
            AND t.League_ID = '" . $_SESSION['League_ID'] . "';";

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
    <div class = "general_container" style = "position: relative; margin-left: 200px;"> 
        <div class = "container_header">
            Your Team
        </div>
        <?php
            if ($result && mysqli_num_rows($result) > 0) {
                // Start the HTML table
                echo "<table border='1'>
                        <thead>
                            <tr>
                                <th>Player Name</th>
                                <th>Player Position</th>
                                <th>Fantasy Points</th>
                            </tr>
                        </thead>
                        <tbody>";
            
                // Loop through each row in the result set
                while ($players = mysqli_fetch_assoc($result)) {
                    // Output each player's data in a table row
                    echo "<tr>
                            <td>" . htmlspecialchars($players['Player_name']) . "</td>
                            <td>" . htmlspecialchars($players['Player_position']) . "</td>
                            <td>" . htmlspecialchars($players['Player_fantasy_points']) . "</td>
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
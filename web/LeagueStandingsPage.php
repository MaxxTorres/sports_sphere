<?php
include "../php-backend/connect.php";
session_start();

$query = "SELECT Team_name, Team_total_points FROM Teams
            WHERE League_ID = '" . $_SESSION['League_ID'] . "'
            GROUP BY Team_total_points DESC;";

$result = mysqli_query($conn, $query);
$teams = mysqli_fetch_assoc($result);
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
            League Standings
        </div>

        <?php
            if ($result && mysqli_num_rows($result) > 0) {
                //Start Table
                echo "<table class = 'general_table'>
                        <thead>
                            <tr>
                                <th>Team Name</th>
                                <th>Fantasy Points</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($teams = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($teams['Team_name']) . "</td>
                            <td>" . htmlspecialchars($teams['Team_total_points']) . "</td>
                          </tr>";
                }
                
                //Close Table
                echo "</tbody>
                    </table>";
            } else {
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
        <a class = "side_bar_button" href = "LeagueSelectPage.php">League Select</a>
        <a class = "side_bar_button" href = "LeagueStandingsPage.php">League Standings</a>
        <a class = "side_bar_button" href = "TeamPage.php">Your Team</a>
        <a class = "side_bar_button" href = "MatchesPage.php">Matches</a>
        <a class = "side_bar_button" href = "DraftPage.php">Draft</a>
        <a class = "side_bar_button" href = "PlayersPage.php">Players</a>
        <a class = "side_bar_button" href = "TradePage.php">Trades</a>
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
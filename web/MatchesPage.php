<?php
include "../php-backend/connect.php";
session_start();

$query = "SELECT team1.Team_name AS Team1_name, team2.Team_name AS Team2_name, Matches.FinalScore AS Match_points
            FROM Matches
            JOIN Teams team1 ON Matches.Team1_ID = team1.Team_ID AND team1.League_ID = '" . $_SESSION['League_ID'] . "'
            JOIN Teams team2 ON Matches.Team2_ID = team2.Team_ID AND team2.League_ID = '" . $_SESSION['League_ID'] . "';";

$result = mysqli_query($conn, $query);
$matches = mysqli_fetch_assoc($result);
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
            Matches
        </div>
        <?php
            if ($result && mysqli_num_rows($result) > 0) {
                // Start the HTML table
                echo "<table class = 'general_table'>
                        <thead>
                            <tr>
                                <th>Team 1</th>
                                <th>Team 2</th>
                                <th>Final Score</th>
                            </tr>
                        </thead>
                        <tbody>";
            
                // Loop through each row in the result set
                while ($matches = mysqli_fetch_assoc($result)) {
                    // Output each player's data in a table row
                    echo "<tr>
                            <td>" . htmlspecialchars($matches['Team1_name']) . "</td>
                            <td>" . htmlspecialchars($matches['Team2_name']) . "</td>
                            <td>" . htmlspecialchars($matches['Match_points']) . "</td>
                          </tr>";
                }
            
                // Close the table
                echo "</tbody>
                    </table>";
            } else {
                // If no results are returned
                echo "<p>No matches found.</p>";
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
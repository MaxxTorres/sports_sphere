<?php
include "../php-backend/connect.php";
session_start();

// Check if the draft is completed
$draft_status_query = "
    SELECT DraftStatus 
    FROM Draft 
    WHERE League_ID = '" . $_SESSION['League_ID'] . "';
";
$draft_status_result = mysqli_query($conn, $draft_status_query);
$draft_status = mysqli_fetch_assoc($draft_status_result);

// Fetch all players in the league if draft is incomplete
$players_query = "
    SELECT p.Player_ID, p.Player_name, p.Player_position, p.Player_fantasy_points, t.Team_name
    FROM Players p
    LEFT JOIN Teams t ON p.Team_ID = t.Team_ID
    WHERE t.League_ID = '" . $_SESSION['League_ID'] . "' 
      AND p.Team_ID IS NULL;
";

$players_result = mysqli_query($conn, $players_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Draft Page</title>
</head>
<body>
    <div class="general_container" style="position: relative; margin-left: 200px; margin-top: 100px"> 
        <div class="container_header">
            Draft
        </div>

        <?php if ($draft_status['DraftStatus'] === 'C'): ?>
            <div class="draft_status">
                <h2>The draft has been completed!</h2>
            </div>
        <?php else: ?>
            <div class="draft_players">
                <h3>Available Players</h3>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Position</th>
                            <th>Fantasy Points</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($players_result) > 0) {
                            while ($player = mysqli_fetch_assoc($players_result)) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($player['Player_name']) . "</td>
                                        <td>" . htmlspecialchars($player['Player_position']) . "</td>
                                        <td>" . htmlspecialchars($player['Player_fantasy_points']) . "</td>
                                        <td>
                                            <form method='POST' action='DraftPlayer.php'>
                                                <input type='hidden' name='player_id' value='" . htmlspecialchars($player['Player_ID']) . "'>
                                                <button type='submit'>Draft</button>
                                            </form>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No players available for draft.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    
    <!-- SIDEBAR -->
    <div id = "side_bar">
        <?php
            if (isset($_SESSION['League_name'])) {
                echo "<h1>" . htmlspecialchars($_SESSION['League_name']) . "</h1>";
            } else {
                echo "<h1>No league selected.</h1>";
            }
            echo "<p>" . htmlspecialchars($_SESSION['role']) . "</p>";
        ?>
        <div style = "margin: 5px; margin-left: 20px; font-size: 14px;">
            <a style = "padding: 5px; color: lightgrey; text-decoration: underline;" class = "side_bar_button" href = "../php-backend/logout.php">Log out</a>
            <a style = "padding: 5px; color: lightgrey; text-decoration: underline;" class = "side_bar_button" href = "LeagueSelectPage.php">League Select</a>
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

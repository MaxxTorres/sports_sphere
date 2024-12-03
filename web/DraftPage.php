

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


$query = "SELECT Player_ID, Player_name, Player_position, Player_fantasy_points
            FROM Players p
            WHERE p.Team_ID IS NULL AND p.Player_availability = 'A' AND p.League_ID = '" . $_SESSION['League_ID'] . "';";
            

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Draft</title>
</head>
<body>
    

    <div class = "general_container" style = "position: relative; margin-left: 200px; margin-top: 100px"> 
        <div class = "container_header">
            Draft
        </div>
        <?php if (isset($draft_status['DraftStatus']) && $draft_status['DraftStatus'] === 'C'): ?>
            <div> Draft had been completed </div>
        <?php else: ?>
            <table class="general_table">
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
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($player = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($player['Player_name']) . "</td>
                                    <td>" . htmlspecialchars($player['Player_position']) . "</td>
                                    <td>" . htmlspecialchars($player['Player_fantasy_points']) . "</td>
                                    <td>
                                        <form action='DraftPlayer.php' method='POST'>
                                            <input type='hidden' name='player_id' value='" . htmlspecialchars($player['Player_ID']) . "'>
                                            <button class='button' type='submit' >Draft</button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No available players.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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




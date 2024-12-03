<?php
include "../php-backend/connect.php";
session_start();
$query = "
    SELECT p.Player_ID, p.Player_name, p.Player_position, p.Player_fantasy_points, t.Team_name
    FROM Players p
    INNER JOIN Teams t ON p.Team_ID = t.Team_ID
    WHERE t.League_ID = '" . $_SESSION['League_ID'] . "'
    AND t.User_ID != '". $_SESSION['User_ID'] . "';";

$result = mysqli_query($conn, $query);
$players = mysqli_fetch_assoc($result);

$pending_trades_query = "
    SELECT pt.trade_id, pt.offering_user_id, pt.target_user_id, pt.offering_player_id, pt.target_player_id, pt.status,
           op.Player_name AS offering_player_name, tp.Player_name AS target_player_name,
           ot.Team_name AS offering_team_name, tt.Team_name AS target_team_name
    FROM PendingTrades pt
    INNER JOIN Players op ON pt.offering_player_id = op.Player_ID
    INNER JOIN Players tp ON pt.target_player_id = tp.Player_ID
    INNER JOIN Teams ot ON op.Team_ID = ot.Team_ID
    INNER JOIN Teams tt ON tp.Team_ID = tt.Team_ID
    WHERE (pt.offering_user_id = '" . $_SESSION['User_ID'] . "' OR pt.target_user_id = '" . $_SESSION['User_ID'] . "')
      AND pt.status = 'Pending';
";
$pending_trades_result = mysqli_query($conn, $pending_trades_query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>NFL League</title>
</head>
<body>
    <div class="general_container" style="position: relative; margin-left: 200px; margin-top: 100px"> 
        <div class="container_header">
            Trade
        </div>

        <!-- Player List -->
        <table class="general_table">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Player Name</th>
                    <th>Position</th>
                    <th>Fantasy Points</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['Team_name']) . "</td>
                                <td>" . htmlspecialchars($row['Player_name']) . "</td>
                                <td>" . htmlspecialchars($row['Player_position']) . "</td>
                                <td>" . htmlspecialchars($row['Player_fantasy_points']) . "</td>
                                <td>
                                    <a href='TradeConfirmation.php?player_id=" . htmlspecialchars($row['Player_ID']) . "' class='button'>Trade</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No players available for trade.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <h2>Pending Trades</h2>
        <table class="general_table">
            <thead>
                <tr>
                    <th>Offering Team</th>
                    <th>Offering Player</th>
                    <th>Target Team</th>
                    <th>Target Player</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
        <?php
        if (mysqli_num_rows($pending_trades_result) > 0) {
            while ($row = mysqli_fetch_assoc($pending_trades_result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['offering_team_name']) . "</td>
                        <td>" . htmlspecialchars($row['offering_player_name']) . "</td>
                        <td>" . htmlspecialchars($row['target_team_name']) . "</td>
                        <td>" . htmlspecialchars($row['target_player_name']) . "</td>
                        <td>";
                
                // Check if the current user is the target of the trade
                if ($row['target_user_id'] == $_SESSION['User_ID']) {
                    echo "<form method='POST' action='ProcessTrade.php' style='display:inline-block;'>
                            <input type='hidden' name='trade_id' value='" . htmlspecialchars($row['trade_id']) . "'>
                            <button type='submit' name='action' value='accept' >Accept</button>
                        </form>
                        <form method='POST' action='ProcessTrade.php' style='display:inline-block;'>
                            <input type='hidden' name='trade_id' value='" . htmlspecialchars($row['trade_id']) . "'>
                            <button type='submit' name='action' value='reject'>Reject</button>
                        </form>";
                } else {
                    echo "N/A";
                }

                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No pending trades found.</td></tr>";
        }
        ?>
    </tbody>
        </table>

       
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

           

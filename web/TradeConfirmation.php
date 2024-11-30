<?php
include "../php-backend/connect.php";
session_start();


$trade_player_id = $_GET['player_id'];

$trade_player_query = "
    SELECT p.Player_name, p.Player_position, p.Player_fantasy_points, t.Team_name
    FROM Players p
    INNER JOIN Teams t ON p.Team_ID = t.Team_ID
    WHERE p.Player_ID = '$trade_player_id';
";
$trade_player_result = mysqli_query($conn, $trade_player_query);
$trade_player = mysqli_fetch_assoc($trade_player_result);


$user_players_query = "
    SELECT p.Player_ID, p.Player_name, p.Player_position, p.Player_fantasy_points, t.Team_name
    FROM Players p
    INNER JOIN Teams t ON p.Team_ID = t.Team_ID
    WHERE t.League_ID = '" . $_SESSION['League_ID'] . "'
    AND t.User_ID = '". $_SESSION['User_ID'] . "';";
$user_players_result = mysqli_query($conn, $user_players_query);


if (!isset($_SESSION['pending_trades'])) {
    $_SESSION['pending_trades'] = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['your_player_id'])) {
    $your_player_id = $_POST['your_player_id'];

    
    $your_player_query = "
        SELECT p.Player_name, t.Team_name
        FROM Players p
        INNER JOIN Teams t ON p.Team_ID = t.Team_ID
        WHERE p.Player_ID = '$your_player_id';
    ";
    $your_player_result = mysqli_query($conn, $your_player_query);
    $your_player = mysqli_fetch_assoc($your_player_result);

    
    $_SESSION['pending_trades'][] = [
        'your_player' => $your_player,
        'trade_player' => $trade_player
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Trade Confirmation</title>
</head>
<body>
    <div class="general_container" style="margin: 20px;">
        <div class="container_header">Select Your Player to Offer</div>
        <p>Trading for: <strong><?php echo htmlspecialchars($trade_player['Player_name']); ?></strong> from Team: <strong><?php echo htmlspecialchars($trade_player['Team_name']); ?></strong></p>
        
        <form method="POST">
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
                    if (mysqli_num_rows($user_players_result) > 0) {
                        while ($row = mysqli_fetch_assoc($user_players_result)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['Player_name']) . "</td>
                                    <td>" . htmlspecialchars($row['Player_position']) . "</td>
                                    <td>" . htmlspecialchars($row['Player_fantasy_points']) . "</td>
                                    <td>
                                        <button type='submit' name='your_player_id' value='" . htmlspecialchars($row['Player_ID']) . "'>Trade With</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>You have no players to trade.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
        <div class="pending_trades">
            <h3>Pending Trades</h3>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Your Player</th>
                        <th>Your Team</th>
                        <th>Trade Player</th>
                        <th>Trade Team</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($_SESSION['pending_trades'])) {
                        foreach ($_SESSION['pending_trades'] as $trade) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($trade['your_player']['Player_name']) . "</td>
                                    <td>" . htmlspecialchars($trade['your_player']['Team_name']) . "</td>
                                    <td>" . htmlspecialchars($trade['trade_player']['Player_name']) . "</td>
                                    <td>" . htmlspecialchars($trade['trade_player']['Team_name']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No pending trades.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

        
    </div>
    

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


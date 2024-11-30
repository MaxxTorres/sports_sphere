<?php
include "../php-backend/connect.php";
session_start();

// Get the ID of the player being traded for
$trade_player_id = $_GET['player_id'];

// Fetch details of the player being traded for
$trade_player_query = "
    SELECT p.Player_ID, p.Player_name, p.Player_position, p.Player_fantasy_points, t.Team_name, t.User_ID AS target_user_id
    FROM Players p
    INNER JOIN Teams t ON p.Team_ID = t.Team_ID
    WHERE p.Player_ID = '$trade_player_id';
";
$trade_player_result = mysqli_query($conn, $trade_player_query);
$trade_player = mysqli_fetch_assoc($trade_player_result);

// Fetch the current user's players
$user_players_query = "
    SELECT p.Player_ID, p.Player_name, p.Player_position, p.Player_fantasy_points, t.Team_name
    FROM Players p
    INNER JOIN Teams t ON p.Team_ID = t.Team_ID
    WHERE t.League_ID = '" . $_SESSION['League_ID'] . "'
    AND t.User_ID = '" . $_SESSION['User_ID'] . "';
";
$user_players_result = mysqli_query($conn, $user_players_query);

// Handle form submission for a trade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['your_player_id'])) {
    $your_player_id = $_POST['your_player_id'];
    $offering_user_id = $_SESSION['User_ID']; // Current user offering the trade
    $target_user_id = $trade_player['target_user_id']; // Target user owning the player

    // Insert the trade into the PendingTrades table
    $insert_trade_query = "
        INSERT INTO PendingTrades (offering_user_id, target_user_id, offering_player_id, target_player_id, status)
        VALUES ('$offering_user_id', '$target_user_id', '$your_player_id', '$trade_player_id', 'Pending');
    ";
    mysqli_query($conn, $insert_trade_query);

    // Redirect to TradePage with a success message
    header("Location: TradePage.php?trade=success");
    exit();
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
    <div class="general_container" style="position: relative; margin-left: 200px; margin-top: 100px">
        <div class="container_header">Select Your Player to Offer</div>
        <p>Trading for: <strong><?php echo htmlspecialchars($trade_player['Player_name']); ?></strong> from Team: <strong><?php echo htmlspecialchars($trade_player['Team_name']); ?></strong></p>
        
        <form method="POST">
            <table class="general_table" style="margin-bottom: 0; margin-top:0">
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



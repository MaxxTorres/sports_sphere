<?php
include "../php-backend/connect.php";
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_id'])) {
    $player_id = (int) $_POST['player_id']; // Safely cast to an integer
    $user_id = $_SESSION['User_ID'];
    $league_id = $_SESSION['League_ID'];
    $team_query = "SELECT Team_ID 
                    FROM Teams 
                    WHERE User_ID = '$user_id' LIMIT 1";
    $team_result = mysqli_query($conn, $team_query);

    if ($team_result && mysqli_num_rows($team_result) > 0) {
        $team = mysqli_fetch_assoc($team_result);
        $team_id = $team['Team_ID'];

    $query = "UPDATE Players 
              SET Team_ID = '$team_id' 
              WHERE Player_ID = '$player_id' AND Team_ID is NULL;";

    // Execute the query and check for errors
    if (mysqli_query($conn, $query)) {
        echo "Player signed successfully.";
    } else {
        die("Error updating player: " . mysqli_error($conn));
    }
}}

$query = "SELECT Player_ID, Player_name, Player_position, Player_fantasy_points
            FROM Players p
            WHERE p.Team_ID IS NULL AND p.League_ID = '" . $_SESSION['League_ID'] . "';";

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
    <!-- ADD PLAYER (for commissioners only) -->
    <?php
        if($_SESSION['role'] == "Commissioner") {
            echo 
            "
            <div class = 'general_container' style = 'position: relative; margin-left: 200px; margin-top: 50px'>
                <div class = 'container_header'>Add Player</div>
                    <form action='../php-backend/add_player.php' method='post' autocomplete='off' style = 'margin: 20px;'>     
                        Player name: <input type='text' id='p_name' name='p_name' required> <br>
                        Player position (1-3 letters): <input type='text' id='p_pos' name='p_pos' required> <br>
                        Real Team: <input type='text' id='p_team' name='p_team' required> <br>
                        <input type='submit' 'value='Add' class='button' style='margin-left: 0px; width: 100%;'>
                    </form>
            </div>
            ";
        }
    ?>

    <div class = "general_container" style = "position: relative; margin-left: 200px; margin-top: 100px"> 
        <div class = "container_header">
            Available Players
        </div>
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
                                    <form action='' method='POST'>
                                        <input type='hidden' name='player_id' value='" . htmlspecialchars($player['Player_ID']) . "'>
                                        <button class='button' type='submit' >Sign</button>
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
<?php
include "../php-backend/connect.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['User_ID']) || !isset($_SESSION['League_ID'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['User_ID'];
$league_id = $_SESSION['League_ID'];

// Check if the logged-in user is the league commissioner
$commissioner_query = "
    SELECT League_commissioner 
    FROM Leagues 
    WHERE League_ID = '$league_id';
";
$commissioner_result = mysqli_query($conn, $commissioner_query);
$commissioner_row = mysqli_fetch_assoc($commissioner_result);

if (!$commissioner_row) {
    die("League not found.");
}

$is_commissioner = $commissioner_row['League_commissioner'] == $user_id;

// Handle admin delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id']) && $is_commissioner) {
    $delete_user_id = $_POST['delete_user_id'];

    $team_query = " SELECT Team_ID
                    FROM Teams
                    Where User_ID = '$delete_user_id' AND League_ID = '$league_id';";
    $team_result = mysqli_query($conn, $team_query);


    if ($team_result && mysqli_num_rows($team_result) > 0) {
        $team_row = mysqli_fetch_assoc($team_result);
        $team_id = $team_row['Team_ID'];

        // Set Team_ID to NULL for all players on this team
        $update_players_query = "
            UPDATE Players 
            SET Team_ID = NULL 
            WHERE Team_ID = '$team_id';
        ";
        if (!mysqli_query($conn, $update_players_query)) {
            $message = "Error updating players: " . mysqli_error($conn);
        }
        $delete_team_query = "
            DELETE FROM Teams 
            WHERE Team_ID = '$team_id';
        ";
        if (mysqli_query($conn, $delete_team_query)) {
            $message = "Team deleted successfully.";
        } else {
            $message = "Error deleting user: " . mysqli_error($conn);
        }
    } else {
        $message = "Team not found for thatuser.";
    }

}

// Handle user profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $update_query = "
        UPDATE User_table 
        SET User_fullname = '$fullname', 
            User_username = '$username', 
            User_email = '$email', 
            User_password = '$password' 
        WHERE User_ID = '$user_id';
    ";

    if (mysqli_query($conn, $update_query)) {
        $message = "Profile updated successfully.";
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch all users in the same league for admin to view
if ($is_commissioner) {
    $users_query = "
        SELECT Team_ID, Team_name, User_ID
        FROM Teams
        WHERE League_ID = '$league_id' AND User_ID != '$user_id';
    ";
    $users_result = mysqli_query($conn, $users_query);
}
// Generate team_ID
$get_last_ID_sql = "SELECT Team_ID
                        FROM Teams
                        ORDER BY Team_ID DESC
                        LIMIT 1; ";
$last_ID = mysqli_query($conn, $get_last_ID_sql);
$row = mysqli_fetch_assoc($last_ID);
$new_user_ID = $row['Team_ID'] + 1;

// Fetch current user's information for updating profile
$user_query = "
    SELECT User_fullname, User_username, User_email 
    FROM User_table
    WHERE User_ID = '$user_id';
";
$user_result = mysqli_query($conn, $user_query);
$current_user = mysqli_fetch_assoc($user_result);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_team'])) {
    $team_name = mysqli_real_escape_string($conn, $_POST['team_name']);
    $team_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

    // Check if the provided User_ID exists in User_table
    $user_check_query = "
        SELECT User_ID 
        FROM User_table 
        WHERE User_ID = '$team_user_id';
    ";
    $user_check_result = mysqli_query($conn, $user_check_query);

    if (mysqli_num_rows($user_check_result) > 0) {
        $league_user_check_query = "
            SELECT User_ID 
            FROM Teams 
            WHERE User_ID = '$team_user_id' AND League_ID = '$league_id';
        ";
        $league_user_check_result = mysqli_query($conn, $league_user_check_query);
        if (mysqli_num_rows($league_user_check_result) > 0) {
            $message = "Error: The user is already part of this league.";
        } else {
            // Insert the new team
            $insert_team_query = "
                INSERT INTO Teams (Team_ID, Team_name, User_ID, League_ID, Team_total_points, Team_ranking, Team_status)
                VALUES ('$new_user_ID', '$team_name', '$team_user_id', '$league_id', '0', '0', 'A' );
            ";

            if (mysqli_query($conn, $insert_team_query)) {
                $message = "Team added successfully.";
            } else {
                $message = "Error adding team: " . mysqli_error($conn);
            }
        }
    } else {
        $message = "Error: The provided User_ID does not exist.";
    }
}
// Generate match_ID
$get_last_match_ID_sql = "SELECT Match_ID
                        FROM Matches
                        ORDER BY Match_ID DESC
                        LIMIT 1; ";
$last_match_ID = mysqli_query($conn, $get_last_match_ID_sql);
$row_match = mysqli_fetch_assoc($last_match_ID);
$new_user_match_ID = $row_match['Match_ID'] + 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_match']) && $is_commissioner) {
    $team1_id = mysqli_real_escape_string($conn, $_POST['team1_id']);
    $team2_id = mysqli_real_escape_string($conn, $_POST['team2_id']);
    $match_date = mysqli_real_escape_string($conn, $_POST['match_date']);
    $final_score = mysqli_real_escape_string($conn, $_POST['final_score']);
    $winner = mysqli_real_escape_string($conn, $_POST['winner']);

    // Validate that Team 1 and Team 2 are not the same
    if ($team1_id === $team2_id) {
        $message = "Error: Team 1 and Team 2 cannot be the same.";
    } else {
        // Check if both teams exist in the league
        $team_check_query = "
            SELECT Team_ID 
            FROM Teams 
            WHERE (Team_ID = '$team1_id' OR Team_ID = '$team2_id') AND League_ID = '$league_id';
        ";
        $team_check_result = mysqli_query($conn, $team_check_query);

        if (mysqli_num_rows($team_check_result) === 2) { // Both teams must exist
            $final_score = !empty($final_score) ? "'$final_score'" : "NULL";
            $winner = !empty($winner) ? "'$winner'" : "NULL";

            $insert_match_query = "
                INSERT INTO Matches (Match_ID, Team1_ID, Team2_ID, MatchDate, FinalScore, Winner)
                VALUES ('$new_user_match_ID', '$team1_id', '$team2_id', '$match_date', $final_score, $winner);
            ";
            if (mysqli_query($conn, $insert_match_query)) {
                $message = "Match added successfully.";
            } else {
                $message = "Error adding match: " . mysqli_error($conn);
            }
        } else {
            $message = "Error: One or both teams do not exist in the league.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Settings</title>
</head>
<body>
    <?php if ($is_commissioner): ?>
    <div class = 'general_container' style = 'position: relative; margin-left: 200px; margin-top: 50px'>
        <div class="container_header">
            Team Management
        </div>
        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        
            <table class="general_table">
                <thead>
                    <tr>
                        <th>Team ID</th>
                        <th>Team name</th>
                        <th>User ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($users_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Team_ID']); ?></td>
                                <td><?php echo htmlspecialchars($row['Team_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['User_ID']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_user_id" value="<?php echo $row['User_ID']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')" class = "button">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No users to manage in this league.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
    </div>
    <div class = 'general_container' style = 'position: relative; margin-left: 200px; margin-top: 50px'>
        <div class="container_header">
            Add New Match
        </div>
        <form method="POST">
            <label for="team1_id">Team 1 ID:</label>
            <input type="number" id="team1_id" name="team1_id" required><br>

            <label for="team2_id">Team 2 ID:</label>
            <input type="number" id="team2_id" name="team2_id" required><br>

            <label for="match_date">Match Date:</label>
            <input type="date" id="match_date" name="match_date" required><br>

            <label for="final_score">Final Score:</label>
            <input type="text" id="final_score" name="final_score"><br>

            <label for="winner">Winner:</label>
            <input type="text" id="winner" name="winner"><br>

            <button type="submit" name="add_match" class="button">Add Match</button>
        </form>
    </div>
    <div class = 'general_container' style = 'position: relative; margin-left: 200px; margin-top: 50px'>
        <div class="container_header">
            Add New Team
        </div>
        <form method="POST">
            <label for="team_name">Team Name:</label>
            <input type="text" id="team_name" name="team_name" required><br>

            <label for="user_id">User ID:</label>
            <input type="number" id="user_id" name="user_id" required><br>

            <button type="submit" name="add_team" class = "button">Add Team</button>
        </form>
    </div>
    <?php endif; ?>
    <div class = 'general_container' style = 'position: relative; margin-left: 200px; margin-top: 50px'>
        <div class="container_header">
            Update Profile
        </div>
        <form method="POST">
            <label for="team_name">Full Name:</label>
            <input type="text" id="team_name" name="team_name" value="<?php echo htmlspecialchars($current_user['User_fullname']); ?>" required><br>

            <label for="user_id">Username:</label>
            <input type="number" id="user_id" name="user_id" value="<?php echo htmlspecialchars($current_user['User_username']); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_user['User_email']); ?>" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit" name="update_profile" class = "button">Update Profile</button>
        </form>
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

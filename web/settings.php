<?php
include "../php-backend/connect.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['User_ID']) || !isset($_SESSION['League_ID'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['User_ID'];
$league_id = $_SESSION['League_ID'];

// Check if the logged-in user is the league commissioner (admin)
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

    $delete_query = "
        DELETE FROM Teams 
        WHERE User_ID = '$delete_user_id' AND User_ID != '$user_id';
    ";

    if (mysqli_query($conn, $delete_query)) {
        $message = "Team deleted successfully.";
    } else {
        $message = "Error deleting user: " . mysqli_error($conn);
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

// Fetch current user's information for updating profile
$user_query = "
    SELECT User_fullname, User_username, User_email 
    FROM User_table
    WHERE User_ID = '$user_id';
";
$user_result = mysqli_query($conn, $user_query);
$current_user = mysqli_fetch_assoc($user_result);
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
    <div class="general_container" style="position: relative; margin-left: 200px; margin-top: 100px">
    <div class="container_header">
            Settings
        </div>
        
        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($is_commissioner): ?>
            <h2>Manage Users in League</h2>
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
        <?php endif; ?>

        <h2>Update Profile</h2>
        <form method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($current_user['User_fullname']); ?>" required><br>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($current_user['User_username']); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_user['User_email']); ?>" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit" name="update_profile">Update Profile</button>
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

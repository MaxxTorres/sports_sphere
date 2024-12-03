<?php
include "../php-backend/connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_id'])) {
    $player_id = $_POST['player_id'];

    // Get the current user's team ID
    $team_query = "
        SELECT t.Team_ID
        FROM Teams t
        WHERE t.User_ID = '" . $_SESSION['User_ID'] . "'
        AND t.League_ID = '" . $_SESSION['League_ID'] . "';";
    $team_result = mysqli_query($conn, $team_query);
    $team = mysqli_fetch_assoc($team_result);

    if ($team) {
        $team_id = $team['Team_ID'];

        // Assign the player to the user's team
        $update_player_query = "
            UPDATE Players
            SET Team_ID = '$team_id'
            WHERE Player_ID = '$player_id';
        ";
        mysqli_query($conn, $update_player_query);

        // Check if there are any undrafted players left
        $remaining_players_query = "
            SELECT COUNT(*) AS undrafted_count
            FROM Players p
            WHERE p.Team_ID IS NULL;
        ";
        $remaining_players_result = mysqli_query($conn, $remaining_players_query);
        $remaining_players = mysqli_fetch_assoc($remaining_players_result);

        // If no undrafted players left, mark draft as completed
        if ($remaining_players['undrafted_count'] == 0) {
            $complete_draft_query = "
                UPDATE Draft
                SET DraftStatus = 'C'
                WHERE League_ID = '" . $_SESSION['League_ID'] . "';
            ";
            mysqli_query($conn, $complete_draft_query);
        }
    }

    // Redirect back to DraftPage
    header("Location: DraftPage.php");
    exit();
}
?>



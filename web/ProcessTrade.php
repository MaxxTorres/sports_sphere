<?php
include "../php-backend/connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trade_id'], $_POST['action'])) {
    $trade_id = $_POST['trade_id'];
    $action = $_POST['action'];

    // Fetch trade details
    $trade_query = "
        SELECT offering_player_id, target_player_id
        FROM PendingTrades
        WHERE trade_id = '$trade_id' AND status = 'Pending';
    ";
    $trade_result = mysqli_query($conn, $trade_query);

    if (!$trade_result || mysqli_num_rows($trade_result) == 0) {
        die("Trade not found or already processed. Error: " . mysqli_error($conn));
    }

    $trade = mysqli_fetch_assoc($trade_result);

    if ($action === 'accept') {
        // Fetch team IDs for the offering and target players
        $offering_player_team_query = "
            SELECT Team_ID 
            FROM Players 
            WHERE Player_ID = '" . $trade['offering_player_id'] . "';
        ";
        $offering_team_result = mysqli_query($conn, $offering_player_team_query);
        if (!$offering_team_result) {
            die("Error fetching offering player's team: " . mysqli_error($conn));
        }
        $offering_team_id = mysqli_fetch_assoc($offering_team_result)['Team_ID'];

        $target_player_team_query = "
            SELECT Team_ID 
            FROM Players 
            WHERE Player_ID = '" . $trade['target_player_id'] . "';
        ";
        $target_team_result = mysqli_query($conn, $target_player_team_query);
        if (!$target_team_result) {
            die("Error fetching target player's team: " . mysqli_error($conn));
        }
        $target_team_id = mysqli_fetch_assoc($target_team_result)['Team_ID'];

        // Swap team IDs
        $update_offering_player_query = "
            UPDATE Players
            SET Team_ID = '$target_team_id'
            WHERE Player_ID = '" . $trade['offering_player_id'] . "';
        ";
        if (!mysqli_query($conn, $update_offering_player_query)) {
            die("Error updating offering player: " . mysqli_error($conn));
        }

        $update_target_player_query = "
            UPDATE Players
            SET Team_ID = '$offering_team_id'
            WHERE Player_ID = '" . $trade['target_player_id'] . "';
        ";
        if (!mysqli_query($conn, $update_target_player_query)) {
            die("Error updating target player: " . mysqli_error($conn));
        }

        // Update trade status to 'Accepted'
        $update_trade_query = "
            UPDATE PendingTrades
            SET status = 'Accepted'
            WHERE trade_id = '$trade_id';
        ";
        if (!mysqli_query($conn, $update_trade_query)) {
            die("Error updating trade status: " . mysqli_error($conn));
        }
    } elseif ($action === 'reject') {
        // Update trade status to 'Rejected'
        $update_trade_query = "
            UPDATE PendingTrades
            SET status = 'Rejected'
            WHERE trade_id = '$trade_id';
        ";
        if (!mysqli_query($conn, $update_trade_query)) {
            die("Error rejecting trade: " . mysqli_error($conn));
        }
    }

    header("Location: TradePage.php");
    exit();
}

?>

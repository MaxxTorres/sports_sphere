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
    $trade = mysqli_fetch_assoc($trade_result);

    if (!$trade) {
        die("Trade not found or already processed.");
    }

    if ($action === 'accept') {
        // Swap Team_IDs of the players
        $swap_query = "
            UPDATE Players p1, Players p2
            SET p1.Team_ID = (SELECT Team_ID FROM Players WHERE Player_ID = '" . $trade['target_player_id'] . "'),
                p2.Team_ID = (SELECT Team_ID FROM Players WHERE Player_ID = '" . $trade['offering_player_id'] . "')
            WHERE p1.Player_ID = '" . $trade['offering_player_id'] . "'
              AND p2.Player_ID = '" . $trade['target_player_id'] . "';
        ";
        mysqli_multi_query($conn, $swap_query);

        // Update trade status to 'Accepted'
        $update_trade_query = "
            UPDATE PendingTrades
            SET status = 'Accepted'
            WHERE trade_id = '$trade_id';
        ";
        mysqli_query($conn, $update_trade_query);
    } elseif ($action === 'reject') {
        // Update trade status to 'Rejected'
        $update_trade_query = "
            UPDATE PendingTrades
            SET status = 'Rejected'
            WHERE trade_id = '$trade_id';
        ";
        mysqli_query($conn, $update_trade_query);
    }

    header("Location: PendingTrades.php");
    exit();
}
?>


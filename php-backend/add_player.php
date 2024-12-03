<?php
include "connect.php";

session_start();

$p_name = $_POST['p_name'];
$p_pos = $_POST['p_pos'];
$p_team = $_POST['p_team'];
$p_sport = "";
$p_points = 15; 

if ($_SESSION['League_ID'] == 2001) {
    $p_sport = 'BB';
} elseif ($_SESSION['League_ID'] == 2002) {
    $p_sport = 'FTB';
} elseif ($_SESSION['League_ID'] == 2003) {
    $p_sport = 'SB';
}

// Generate Player_ID
$get_last_ID_sql = "SELECT Player_ID
                        FROM Players
                        ORDER BY Player_ID DESC
                        LIMIT 1; ";
$last_ID = mysqli_query($conn, $get_last_ID_sql);
$row = mysqli_fetch_assoc($last_ID);
$new_player_ID = $row['Player_ID'] + 1;

// Prepare SQL query
$sql_insert_player = "INSERT INTO Players (Player_ID, Player_name, Player_sport, Player_position, Player_Real_Team, Player_fantasy_points, League_ID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt_insert_player = mysqli_prepare($conn, $sql_insert_player);
mysqli_stmt_bind_param($stmt_insert_player, "isssssi", $new_player_ID, $p_name, $p_sport, $p_pos, $p_team, $p_points, $_SESSION['League_ID']);
$result_insert_player = mysqli_stmt_execute($stmt_insert_player);

if ($result_insert_player) {
    header("Location: ../web/PlayersPage.php");
} else {
    echo "Could Not Add Player";
}
exit();

mysqli_stmt_close($stmt_insert_player);
mysqli_close($conn);
?>

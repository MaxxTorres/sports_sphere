<?php
include "connect.php";

session_start();

$p_id = $_POST['p_id'];

// Prepare SQL query
$sql_remove_player = "UPDATE Players
                        SET Team_ID = NULL, Player_availability = 'A'
                        WHERE Player_ID = $p_id;";
$result_remove_player = mysqli_query($conn, $sql_remove_player);


if ($result_remove_player) {
    header("Location: ../web/TeamPage.php");
} else {
    echo "Could Not Remove Player";
}
exit();

mysqli_close($conn);
?>

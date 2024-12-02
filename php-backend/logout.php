<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../web/index.php"); // Redirect to homepage after logout
    exit();
?>
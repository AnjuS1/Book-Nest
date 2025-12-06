<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    var_dump($_SESSION); 
    exit;
?>

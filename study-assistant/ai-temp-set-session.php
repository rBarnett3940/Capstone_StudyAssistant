<?php
session_start();

if(isset($_GET['course'])) {
    $_SESSION["ai"] = $_GET['course'];
    // You can optionally echo a response if needed
    echo "Session variable 'ai' set to: " . $_SESSION["ai"];
}
?>

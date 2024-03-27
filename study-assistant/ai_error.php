<?php
    # Initialize the session
    session_start();

    # If user is not logged in then redirect him to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
        echo "<script>window.location.href='./login.php';</script>";
    exit;
    }

    if (isset($_SESSION['mode'])) {
        echo "<script>window.location.href='./timetable.php';</script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AI Error</title>
        <link href="./css/main.css" rel="stylesheet" />
        <link href="./css/styles.css" rel="stylesheet" />
        <?php include './includes/bootstrap.php'; ?>
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php include './includes/header.php'; ?>
        <br>
        <div class="container">
            <h1>AI Error!</h1>
            <p>You must select your preferences first before attempting to use the Study Hours Generator. Select the button below in order to do so.</p>
            <a href="./add-preferences-display.php"><button>Add Preferences</button></a>
        </div>
    </body>
</html>
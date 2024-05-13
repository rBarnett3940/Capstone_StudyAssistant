<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='./login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, 
				initial-scale=1.0">
    <title>Pomodoro Timer</title>
    <link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
    <?php include './includes/bootstrap.php'; ?>
    <link rel="stylesheet" href="css/pomodoro.css">
</head>

<body>
    <?php include './includes/header.php'; ?>
    <div class="container">
        <h1>Pomodoro Timer</h1>

        <div id="pomodoro">
            <div id="status"></div>
            <div class="timerDisplayContainer">
                <div id="timerCircle" class="timerCircle"></div>
                <div class="timerDisplay">25:00</div>
            </div>
            <button id="start-btn" class="btn">START</button>
        </div>

        <div class="settings">
            <div id="work">
                <p>Work Duration</p>
                <button class="btn-settings" id="work-plus">+</button>
                <div><span id="work-min">25</span> mins</div>
                <button class="btn-settings" id="work-minus">-</button>
            </div>

            <button id="reset" class="btn">RESET</button>

            <div id="break">
                <p>Break Duration</p>
                <button class="btn-settings" id="break-plus">+</button>
                <div><span id="break-min">5</span> mins</div>
                <button class="btn-settings" id="break-minus">-</button>
            </div>
        </div>
    </div>
    <script src="js/pomodoro.js"></script>
</body>

</html>
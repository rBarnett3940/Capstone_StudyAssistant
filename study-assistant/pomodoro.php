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
    <!-- Page header import -->
    <?php include './includes/header.php'; ?>
    <div class="container">
        <h1>Pomodoro Timer</h1>

        <!-- Pomodoro -->
        <div id="pomodoro">
            <!-- Status -->
            <div id="status"></div>
            <!-- Timer -->
            <div class="timerDisplayContainer">
                <div id="timerCircle" class="timerCircle"></div>
                <div class="timerDisplay">25:00</div>
            </div>
            <!-- Start button -->
            <button id="start-btn" class="btn">START</button>
        </div>

        <!-- Pomodoro settings -->
        <div class="settings">
            <div id="work">
                <!-- Work Duration -->
                <p>Work Duration</p>
                <!-- Up Button -->
                <button class="btn-settings" id="work-plus">+</button>
                <!-- Total Mins -->
                <div><span id="work-min">25</span> mins</div>
                <!-- Down Button -->
                <button class="btn-settings" id="work-minus">-</button>
            </div>

            <!-- Reset Button -->
            <button id="reset" class="btn">RESET</button>

            <div id="break">
                <!-- Break Duration -->
                <p>Break Duration</p>
                <!-- Up Button -->
                <button class="btn-settings" id="break-plus">+</button>
                <!-- Total Mins -->
                <div><span id="break-min">5</span> mins</div>
                <!-- Down Button -->
                <button class="btn-settings" id="break-minus">-</button>
            </div>
        </div>
    </div>
    <!-- Include Javascript -->
    <script src="js/pomodoro.js"></script>
    <!-- Include page footer -->
    <?php include './includes/footer.php'; ?>
</body>
<!-- Include script for notifications -->
<?php include './includes/notifications.php'; ?>

</html>
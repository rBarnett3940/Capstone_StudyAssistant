<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='./login.php';</script>";
    exit;
}
if (!isset($_SESSION['mode'])) {
    echo "<script>window.location.href='./ai_error.php';</script>";
    exit;
}
$courses1 = ["COMP1126", "COMP1127", "COMP1161", "COMP1210", "COMP1220"];
$courses2 = ["COMP2190", "COMP2140", "COMP2171", "COMP2201", "COMP2211", "COMP2340", "COMP2130"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Hours Generator</title>
    <link href="./css/styles.css" rel="stylesheet" />
    <?php include './includes/bootstrap.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
</head>

<body>
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container">
        <div class="d-flex justify-content-around bg-dark p-4">
            <div class="d-flex flex-column bg-light p-2">
                <?php foreach ($courses1 as $course) { ?>
                    <button class="course-btn <?php echo $course; ?>"><?php echo $course; ?></button>
                <?php } ?>
            </div>
            <div class="d-flex flex-column bg-light p-2">
                <?php foreach ($courses2 as $course) { ?>
                    <button class="course-btn <?php echo $course; ?>"><?php echo $course; ?></button>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // When the button is clicked
            $('.course-btn').click(function() {
                var classes = $(this).attr('class').split(' ');
                var secondClass = classes.length > 1 ? classes[1] : null;
                urlLink = `http://127.0.0.1:5000/${secondClass}_ai_starter`
                console.log('Button class:', urlLink);
                var formData = {
                    userID: <?= htmlspecialchars($_SESSION["id"]); ?>,
                    tod: <?= htmlspecialchars($_SESSION["tod"]); ?>,
                    dow: <?= htmlspecialchars($_SESSION["dow"]); ?>,
                    maxHours: <?= htmlspecialchars($_SESSION["maxHours"]); ?>
                };
                // Make an AJAX request to the Flask server
                $.ajax({
                    type: 'POST',
                    url: urlLink, // Flask route to render the template
                    data: formData,
                    success: function(data) {
                        // On success, replace the current HTML content with the received data
                        $('body').html(data);
                    },
                    error: function(xhr, status, error) {
                        // On error, log the error message
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>
</body>

</html>
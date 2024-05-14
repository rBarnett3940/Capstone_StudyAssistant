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
    $courses = ["COMP1126", "COMP1127"];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Study Hours Generator</title>
        <link href="./css/main.css" rel="stylesheet" />
        <link href="./css/styles.css" rel="stylesheet" />
        <?php include './includes/bootstrap.php'; ?>
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php include './includes/header.php'; ?>
        <br>
        <div class="container">
            <?php foreach ($courses as $course){ ?>
                <button class="course-btn <?php echo $course; ?>"><?php echo $course; ?></button>
            <?php } ?>
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
    <?php include './includes/footer.php'; ?>
    </body>
</html>
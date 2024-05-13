<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='./login.php';</script>";
    exit;
}


if (!isset($_SESSION['course_code'])) {
    echo "<script>window.location.href='./ai-temp.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Questions</title>
    <link href="./css/styles.css" rel="stylesheet" />
    <?php include './includes/bootstrap.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
</head>

<body>
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container course">
        <h1><?php echo $_SESSION['course_code'] ?></h1>
        <p style="font-weight: bold;"><?php echo $_SESSION['course_title'] ?></p>
        <form id="prediction-form" method="post" action="">
            <div class="itms">
                <label for="difficulty">Difficulty: <span>(How difficult do you find this course with 10 being the most difficult?)</span></label>
                <br>
                <select id="difficulty" name="difficulty" required>
                    <option value="" disabled selected hidden>Choose Difficulty...</option>
                    <option value="10">10</option>
                    <option value="9">9</option>
                    <option value="8">8</option>
                    <option value="7">7</option>
                    <option value="6">6</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <label for="courses">Courses: <span>(How many total courses are you currently doing?)</span></label>
                <br>
                <select id="courses" name="courses" required>
                    <option value="" disabled selected hidden>Choose Number of Courses...</option>
                    <option value="7">7</option>
                    <option value="6">6</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                </select>
            </div>
            <br>
            <button type="submit" id="generate-btn" class="btn btn-primary">Generate</button>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            // Function to make the Ajax request and return a Promise
            function makeRequest(url, data) {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(response) {
                            resolve(response); // Resolve the Promise with the response
                        },
                        error: function(xhr, status, error) {
                            reject(error); // Reject the Promise with the error
                        }
                    });
                });
            }

            $('#generate-btn').click(async function(event) {
                // Prevent the default form submission behavior
                event.preventDefault();

                var course = $('h1').text().trim();
                console.log(course);

                var urlLink = `http://127.0.0.1:5000/${course}_ai`;
                console.log(urlLink);

                var formData = {
                    Mode: <?php echo $_SESSION["mode"] ?>,
                    Performance: 10,
                    Difficulty: $('#difficulty').val(),
                    Courses: $('#courses').val(),
                    Retention: <?php echo $_SESSION["retention"] ?>,
                    User: <?php echo $_SESSION["id"] ?>,
                    Tod: <?php echo $_SESSION["tod"] ?>,
                    Dow: <?php echo $_SESSION["dow"] ?>,
                    MaxHours: <?php echo $_SESSION["maxHours"] ?>
                };
                console.log(formData);

                try {
                    // Make the Ajax request and wait for the response
                    var response = await makeRequest(urlLink, formData);

                    // Handle the response if needed
                    console.log(response['message']);
                    $.post('./store_session_variable.php', {
                        message: response
                    });
                    window.location.href = `./generated_hours.php`;
                } catch (error) {
                    // On error, log the error message
                    console.error('Error:', error);
                }
            });
        });
    </script>


</body>

</html>
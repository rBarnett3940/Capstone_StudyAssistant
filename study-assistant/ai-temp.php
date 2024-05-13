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

var_dump($_SESSION['generated_hours']);

$courses1 = ["COMP1126", "COMP1127", "COMP1161", "COMP1210", "COMP1220"];
$courses2 = ["COMP2190", "COMP2140", "COMP2171", "COMP2201", "COMP2211", "COMP2340", "COMP2130"];
$titles1 = ["Introduction to Computing I", "Introduction to Computing II", "Object-Oriented Programming", "Mathematics for Computing", "Computing and Society"];
$titles2 = ["Net-Centric Computing", "Software Engineering", "Object Oriented Design and Implementation", "Discrete Mathematics for Computer Science", "Analysis of Algorithms", "Computer Systems Organization", "Systems Programming"];
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
    <script>
        function setSessionAndRedirect(courseCode, courseTitle) {
            // Set PHP session variables using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'set_session_variables.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Redirect to ai_questions.php
                    window.location.href = './ai_questions.php';
                }
            };
            xhr.send('course_code=' + courseCode + '&course_title=' + courseTitle);
        }
    </script>
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container">
        <div class="">
            <h1>First Year Courses</h1>
            <div class="card-container">
                <?php foreach ($courses1 as $index => $course) { ?>
                    <div class="card">
                        <img src="./img/course1.jpg" alt="Denim Jeans" style="width:100%">
                        <div class="card-title">
                            <h1><?php echo $course; ?></h1>
                            <p><?php echo $titles1[$index]; ?></p>
                        </div>
                        <p><button class="course-btn <?php echo $course; ?>" onclick="setSessionAndRedirect('<?php echo $course; ?>', '<?php echo $titles1[$index]; ?>')">Use AI</button></p>
                    </div>
                <?php } ?>
            </div>
            <br><br>
            <h1>Second Year Courses</h1>
            <div class="card-container">
                <?php foreach ($courses2 as $index => $course) { ?>
                    <div class="card">
                        <img src="./img/course1.jpg" alt="Denim Jeans" style="width:100%">
                        <div class="card-title">
                            <h1><?php echo $course; ?></h1>
                            <p><?php echo $titles2[$index]; ?></p>
                        </div>
                        <p><button class="course-btn <?php echo $course; ?>" onclick="setSessionAndRedirect('<?php echo $course; ?>', '<?php echo $titles2[$index]; ?>')">Use AI</button></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</body>

</html>
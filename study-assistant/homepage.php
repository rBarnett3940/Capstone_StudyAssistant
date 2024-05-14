<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include './includes/bootstrap.php'; ?>
    <link href="../study-assistant/css/homepage.css" rel="stylesheet" />
    <style>
        .landing-content {
            margin-top: 60px; /* Adds margin to push content down */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <title>Peli</title>
</head>
<body>
<?php include './includes/homepageheader.php'; ?>
    <!-- Landing section -->
    <section class="landing">
        <!-- Text content -->
        <div class="landing-content">
            <h1>Empower Your Success with Your Personalized Path to Exam Excellence!</h1>
            <p>Say goodbye to exam anxiety and hello to personalized study schedules tailored to your unique needs. With StudyAssistant, you'll effortlessly organize your exam preparation, receive timely reminders, and track your progress every step of the way.</p>
            <a href="../study-assistant/register.php" class="getstarted">Get Started</a>
            <a href="../study-assistant/login.php" class="login">Log In</a>
        </div>
        
        <!-- Image -->
        <img src="../study-assistant/img/landing.jpg" alt="Landing Image" class="landing-image">
    </section>
<?php include './includes/footer.php'; ?>
</body>
</html>

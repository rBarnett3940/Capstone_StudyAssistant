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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./css/styles.css" rel="stylesheet" />
    <link href="./css/homepage.css" rel="stylesheet" />
    <?php include './includes/bootstrap.php'; ?>
    <title>Dashboard</title>
</head>

<body>
    <!-- Include page Header -->
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container">
        <h1 class="welcome">Welcome back, <?php echo $_SESSION['username']; ?>!</h1>
        <section class="landing">
            <!-- Text content -->
            <div class="landing-content">
                <h1>Empower Your Success with Your Personalized Path to Exam Excellence!</h1>
                <p>Say goodbye to exam anxiety and hello to personalized study schedules tailored to your unique needs. With StudyAssistant, you'll effortlessly organize your exam preparation, receive timely reminders, and track your progress every step of the way.</p>
                <a href="../study-assistant/ai-temp.php" class="getstarted">Use AI</a>
            </div>
            <div class="landing-image">
                <!-- Image -->
                <img src="../study-assistant/img/landing.jpg" alt="Landing Image" style="max-width: 500px;">
            </div>
        </section>
        <br>
        <!-- Upcoming Events Container -->
        <div id="events-container">
            <h2>Upcoming Events</h2>
            <hr>
            <br>
            <div id="events">

            </div>
            <!-- Link to timetable -->
            <a href="../study-assistant/timetable.php" class="getstarted">View Timetable</a>
        </div>
    </div>
    <!-- Include page footer -->
    <?php include './includes/footer.php'; ?>
</body>

<script>
    let userID = <?php echo $_SESSION['id']; ?>;
    // Node.js endpoint
    var urlLink = `http://localhost:3000/api/upcoming_events/${userID}`;
    // Fetch upcoming events
    fetch(urlLink)
        .then(response => response.json())
        .then(events => {
            console.log(events[0]);
            const eventsContainer = document.getElementById('events');

            // If no upcoming events
            if (events[0] == null) {
                const eventParagraph = document.createElement('p');

                // Set the text content of the <p> tag to not upcoming events
                eventParagraph.textContent = `You Have No Upcoming Events`;

                // Append the <p> tag to the container element
                eventsContainer.appendChild(eventParagraph);
            } else {
                // Loop through the events array and create <h3> and <p> tags for each event
                events.forEach(event => {
                    // Create a new <p> tag for each event
                    const eventTitle = document.createElement('h3');
                    const eventParagraph = document.createElement('p');

                    // Set the text content of the <h3> and <p> tags with event title and details
                    eventTitle.textContent = `${event.title}`;
                    eventParagraph.textContent = `${event.dayOfWeek} @ (${event.startTime} - ${event.endTime})`;

                    // Append the <p> tag to the container element
                    eventsContainer.appendChild(eventTitle);
                    eventsContainer.appendChild(eventParagraph);
                });
            }
            console.log(events)
            console.log("Loaded Successfully");
        })
        // catch any errors
        .catch(error => console.error('Error fetching events:', error));
</script>
<!-- Include notifications -->
<?php include './includes/notifications.php'; ?>

</html>
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
    <title>Document</title>
</head>

<body>
    <div id="events-container">

    </div>

    <h1>Event Notifications</h1>
    <div id="notifications"></div>
</body>

<script>
    fetch('http://localhost:3000/api/upcoming_events/10')
        .then(response => response.json())
        .then(events => {
            const eventsContainer = document.getElementById('events-container');

            if (events.length == 0) {
                const eventParagraph = document.createElement('p');

                // Set the text content of the <p> tag with event details
                eventParagraph.textContent = `No Upcoming Events`;

                // Append the <p> tag to the container element
                eventsContainer.appendChild(eventParagraph);
            } else {
                // Loop through the events array and create <p> tags for each event
                events.forEach(event => {
                    // Create a new <p> tag for each event
                    const eventParagraph = document.createElement('p');

                    // Set the text content of the <p> tag with event details
                    eventParagraph.textContent = `${event.title} (${event.startTime} - ${event.endTime})`;

                    // Append the <p> tag to the container element
                    eventsContainer.appendChild(eventParagraph);
                });
            }
            console.log(events)
            console.log("Loaded Successfully");
        })
        .catch(error => console.error('Error fetching events:', error));



    function checkNotifications() {
        fetch('http://localhost:3000/api/notifications/10') // Assuming this is the endpoint to fetch notifications
            .then(response => response.json())
            .then(data => {
                // Display notifications to the user
                if (data.length > 0) {
                    alert(data)
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }

    // Call the checkNotifications function periodically
    setInterval(checkNotifications, 30000); // Check every 30 seconds
</script>

</html>
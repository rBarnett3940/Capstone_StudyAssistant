<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='./login.php';</script>";
    exit;
}


if (!isset($_SESSION['message']) || !isset($_SESSION['prediction'])) {
    echo "<script>window.location.href='./ai-temp.php';</script>";
    exit;
}
?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Hours</title>
    <link href="./css/styles.css" rel="stylesheet" />
    <?php include './includes/bootstrap.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Include page header -->
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container course">
        <!-- Echo course code and title AS PAGE TITLE -->
        <h1><?php echo $_SESSION['course_code'] ?></h1>
        <p style="font-weight: bold;"><?php echo $_SESSION['course_title'] ?></p>
        <!-- Message -->
        <p><?php echo $_SESSION['message'] ?></p>
        <!-- Prediction -->
        <h2><?php echo $_SESSION['prediction'] ?></h2>
        <p>If you would like to for your recommeded study hours to be automatically added to your study timetable, please click the button below.</p>
        <!-- Button to add to prediction to timetable -->
        <button id="t-btn">Add to Timetable</button>
    </div>
    <script>
        const button = document.getElementById('t-btn');
        // Event listener for adding prediction to timetable
        button.addEventListener('click', function(event) {
            var course = document.querySelector("h1").innerText;
            var hours = parseInt(document.querySelector("h2").innerText);
            var user = <?php echo $_SESSION["id"] ?>;
            var tod = <?php echo $_SESSION["tod"] ?>;
            var dow = <?php echo $_SESSION["dow"] ?>;
            var maxHours = <?php echo $_SESSION["maxHours"] ?>;
            //window.location.href='./timetable.php';
            console.log("tod", tod)


            // Getting the start hours for each time of the day
            var today = new Date();
            var startTime = new Date(today);
            console.log("test 1", startTime);
            if (tod == 1) {
                startTime.setHours(8, 0, 0, 0);
            } else if (tod == 2) {
                startTime.setHours(12, 0, 0, 0);
            } else if (tod == 3) {
                startTime.setHours(18, 0, 0, 0);
            }
            var todayFormatted = startTime.toISOString();
            console.log("test 2", startTime);
            console.log(todayFormatted);

            // Getting the number of events to be created
            var numEvents = Math.floor(hours / maxHours);
            var remainder = hours % maxHours;
            if (remainder > 0) {
                numEvents++;
            }

            // Call the getEvnts function
            var events = getEvents(user, tod, dow, maxHours, todayFormatted, numEvents, remainder, course, hours, user);

        });

        function getEvents(user, tod, dow, hours, day_start, numEvents, remainder, course, hoursAI) {
            // Call the get eventt endpoint from node.js
            fetch('http://localhost:3000/api/events')
                .then(response => response.json())
                .then(events => {
                    if (events[0] != undefined) {
                        eventsTotal = [];
                        // Get the events for this specific user
                        for (let x in events) {
                            if (events[x]["userID"] == user) {
                                var np = JSON.parse(events[x]["info"]);
                                events[x]["info"] = np;
                                eventsTotal.push(events[x]);
                            }
                        }
                    }
                    // Run check times function
                    console.log("Events Loaded Successfully");
                    checkTimes(eventsTotal, tod, dow, hours, numEvents, remainder, course, hoursAI, user);
                })
                // Catch any errors
                .catch(error => console.error('Error fetching events:', error));
        }

        async function checkTimes(events, tod, dow, hours, numEvents, remainder, course, hoursAI, user) {
            // Define time range and slot duration
            var start = 0;
            var end = 0;
            var duration = hours;
            console.log("duration", duration, tod, dow);


            if (tod == 1) {
                start = 8;
                end = start + 4;
            } else if (tod == 2) {
                start = 12;
                end = start + 6;
            } else if (tod == 3) {
                start = 18;
                end = start + 6;
            }

            const slots = [];
            let currentTime = start;

            // Check for available slots
            do {
                slots.push({
                    start: currentTime,
                    end: currentTime + duration
                });
                currentTime = currentTime + 1;
            } while (currentTime + duration <= end);

            console.log(slots);
            var weekend = [0, 6];
            var eventsInDay = {
                0: 0,
                1: 0,
                2: 0,
                3: 0,
                4: 0,
                5: 0,
                6: 0
            };
            var slotTotal = 0;
            console.log("Type:", typeof(events[1].info.endTime));

            // Checl slots based on day of the week
            if (dow == 1) {
                for (let i = 1; i <= 5; i++) {
                    const availableSlots = [];
                    const UnavailableSlots = [];

                    slots.forEach(slot => {
                        // Check if the slot overlaps with any event
                        const overlap = events.some(event =>
                            slot.start < parseInt(event.info.endTime.split(":")[0]) && slot.end > parseInt(event.info.startTime.split(":")[0]) && parseInt(event.info.daysOfWeek[0]) == i
                        );

                        if (!overlap) {
                            availableSlots.push(slot);
                        }
                    });

                    console.log("1Available", availableSlots);
                    eventsInDay[i] = availableSlots;
                    slotTotal += availableSlots.length;
                }

            } else if (dow == 2) {
                for (let i in weekend) {
                    const availableSlots = [];
                    const UnavailableSlots = [];

                    slots.forEach(slot => {
                        // Check if the slot overlaps with any event
                        const overlap = events.some(event =>
                            slot.start < parseInt(event.info.endTime.split(":")[0]) && slot.end > parseInt(event.info.startTime.split(":")[0]) && parseInt(event.info.daysOfWeek[0]) == weekend[i]
                        );

                        if (!overlap) {
                            availableSlots.push(slot);
                        }
                    });

                    console.log("2Available", availableSlots);
                    eventsInDay[weekend[i]] = availableSlots;
                    slotTotal += availableSlots.length;
                    console.log(eventsInDay);
                }
            } else {
                for (let i = 0; i <= 6; i++) {
                    const availableSlots = [];
                    const UnavailableSlots = [];

                    slots.forEach(slot => {
                        // Check if the slot overlaps with any event
                        const overlap = events.some(event =>
                            slot.start < parseInt(event.info.endTime.split(":")[0]) && slot.end > parseInt(event.info.startTime.split(":")[0]) && parseInt(event.info.daysOfWeek[0]) == i
                        );

                        if (!overlap) {
                            availableSlots.push(slot);
                        }
                    });

                    console.log("Available", availableSlots);
                    slotTotal += availableSlots.length;
                    eventsInDay[i] = availableSlots;
                }
            }
            console.log(eventsInDay);
            console.log("The total amount of slots are", slotTotal);

            var eventIdArr = [];

            for (let j = 0; j < numEvents; j++) {
                var eventId = 0;
                // Getting available event IDs from node.js api
                const response = await fetch('http://localhost:3000/api/EventIds', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch existing event IDs');
                        }
                        return response.json();
                    })
                    .then(existingIds => {
                        // Generate a unique event ID

                        eventId = Math.round(Math.random() * 100000);
                        while (existingIds.includes(eventId)) {
                            eventId = Math.round(Math.random() * 100000);
                        }
                        // Call the callback with the generated event ID
                        eventIdArr.push(eventId);
                        console.log("This", eventId);
                    })
                    .catch(error => {
                        console.error('Error fetching existing event IDs:', error);
                        // Call the callback with null to indicate error
                        callback(null);
                    });
            }

            // Check if there are enough slots available to add events
            if (numEvents > slotTotal) {
                alert("Not enough slots available in timetable. Please enter events manually!");
            } else {
                // if enough slots call finalEvents function
                finalEvents(eventsInDay, hours, numEvents, remainder, course, hoursAI, user, eventIdArr)
            }
        }


        async function finalEvents(eventsInDay, hours, numEvents, remainder, course, hoursAI, user, eventId) {
            console.log("yea", numEvents, remainder, eventId);
            var counter = numEvents
            for (let i = 0; i < numEvents; i++) {
                var temp = 0;
                var key = 0;
                for (let x in eventsInDay) {
                    if (Array.isArray(eventsInDay[x]) && eventsInDay[x].length > temp) {
                        key = x;
                        temp = eventsInDay[x].length;
                    }
                }
                console.log(key, temp);

                // Getting start and end times
                let today = new Date();
                // Calculate the number of days until the next occurrence
                let targetDay = (key - today.getDay() + 7) % 7; 
                let nextDay = new Date(today.getTime() + targetDay * 24 * 60 * 60 * 1000);
                nextDay.setHours(eventsInDay[key][0]["start"], 0, 0, 0);
                var dayFormatted = nextDay.toISOString();
                var startT = `${eventsInDay[key][0]["start"]}:00`;
                var endT = 0;
                if (counter == 1 && remainder != 0) {
                    var endTm = eventsInDay[key][0]["start"] + remainder;
                    endT = `${endTm}:00`;
                } else {
                    endT = `${eventsInDay[key][0]["end"]}:00`;
                }

                counter--;
                // Create event information
                var eventData = {
                    title: course,
                    start: dayFormatted,
                    daysOfWeek: [key.toString()],
                    startTime: startT,
                    endTime: endT,
                    color: '#6082B6',
                    recurring: true,
                    id: eventId[i]
                };
                console.log("EventDATA", eventData);
                console.log("checking", eventsInDay);
                eventsInDay[key].shift();
                // Call save event function
                var temp = await saveEvent(eventData, user);
            }
            setTimeout(5000);
            window.location.href = './timetable.php';
        }




        function saveEvent(eventData, user) {
            // call node.js apit for events to add events
            return fetch('http://localhost:3000/api/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify([{
                        userID: user
                    }, {
                        eventID: eventData.id
                    }, {
                        info: eventData
                    }])
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to save event');
                    }
                    return response.json();
                })
                .then(data => console.log(data.message))
                .catch(error => console.error('Error saving event:', error));
        }

        
    </script>
    <!-- Include page footer -->
    <?php include './includes/footer.php'; ?>
</body>
<!-- Include notifications -->
<?php include './includes/notifications.php'; ?>

</html>
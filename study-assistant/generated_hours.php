<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='./login.php';</script>";
    exit;
}


if (!isset($_SESSION['generated_hours'])) {
    echo "<script>window.location.href='./ai-temp.php';</script>";
    exit;
} else {
    $response = $_SESSION['generated_hours'];
    var_dump($response);
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
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container course">
        <h1><?php echo $_SESSION['course_code'] ?></h1>
        <p style="font-weight: bold;"><?php echo $_SESSION['course_title'] ?></p>
        <p><?php echo $_SESSION['course_code'] ?></p>
        <h2><?php echo $_SESSION['course_code'] ?></h2>
        <p style="display: none;" id="user">{{ user }}</p>
        <p style="display: none;" id="tod">{{ tod }}</p>
        <p style="display: none;" id="dow">{{ dow }}</p>
        <p style="display: none;" id="max-hours">{{ maxHours }}</p>
        <p>If you would like to for your recommeded study hours to be automatically added to your study timetable, please click the button below.</p>
        <button id="t-btn">Add to Timetable</button>
    </div>
    <script>
        const button = document.getElementById('t-btn');
        button.addEventListener('click', function(event) {
            var course = document.querySelector("h1").innerText;
            var hours = parseInt(document.querySelector("h2").innerText);
            var user = parseInt(document.querySelector('#user').innerText);
            var tod = parseInt(document.querySelector('#tod').innerText);
            var dow = parseInt(document.querySelector('#dow').innerText);
            var maxHours = parseInt(document.querySelector('#max-hours').innerText);
            //window.location.href='./timetable.php';
            console.log("tod", tod)


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

            var numEvents = Math.floor(hours / maxHours);
            var remainder = hours % maxHours;
            if (remainder > 0) {
                numEvents++;
            }

            var events = getEvents(user, tod, dow, maxHours, todayFormatted, numEvents, remainder, course, hours, user);



            // Check for existing events
            /*
            fetch('http://localhost:3000/api/events')
                .then(response => response.json())
                .then(events => {
                    // Filter events based on user ID and check for overlaps
                    var filteredEvents = events.filter(event => event.userID === user);
                    var conflictingEvents = filteredEvents.filter(event => {
                        var eventStart = new Date(event.start);
                        return eventStart >= startTime && eventStart < endTime;
                    });
                    if (conflictingEvents.length > 0) {
                        console.error('Conflicting events detected. Unable to add events.');
                        return;
                    }

                    // Add events to the database
                    var eventPromises = [];
                    for (let i = 0; i < numEvents; i++) {
                        var eventData = {
                            title: course,
                            start: startTime.toISOString(), // Convert to ISO string format
                            daysOfWeek: [dow.toString()], // Convert to array with string value
                            startTime: startTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), // Format as HH:mm
                            endTime: '', // Set the end time accordingly
                            color: '#00FF00',
                            recurring: true,
                            id: Math.round(Math.random() * 100000) // Generate a random ID
                        };
                        // Calculate end time for the event (30 minutes by default)
                        var endTime = new Date(startTime);
                        endTime.setMinutes(endTime.getMinutes() + 30);
                        eventData.endTime = endTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // Format as HH:mm
                        
                        // Add event data to the database
                        var eventPromise = saveEvent(eventData);
                        eventPromises.push(eventPromise);
                        
                        // Increment start time for the next event
                        startTime.setMinutes(startTime.getMinutes() + maxHours * 60);
                    }

                    // Wait for all event promises to resolve
                    Promise.all(eventPromises)
                        .then(() => console.log('Events added successfully'))
                        .catch(error => console.error('Error adding events:', error));
                })
                .catch(error => console.error('Error fetching events:', error));*/
        });

        function getEvents(user, tod, dow, hours, day_start, numEvents, remainder, course, hoursAI) {
            fetch('http://localhost:3000/api/events')
                .then(response => response.json())
                .then(events => {
                    if (events[0] != undefined) {
                        eventsTotal = [];
                        for (let x in events) {
                            if (events[x]["userID"] == user) {
                                var np = JSON.parse(events[x]["info"]);
                                events[x]["info"] = np;
                                eventsTotal.push(events[x]);
                            }
                        }
                    }
                    console.log("Events Loaded Successfully");
                    checkTimes(eventsTotal, tod, dow, hours, numEvents, remainder, course, hoursAI, user);
                })
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

                    console.log("3Available", availableSlots);
                    slotTotal += availableSlots.length;
                    eventsInDay[i] = availableSlots;
                }
            }
            console.log(eventsInDay);
            console.log("The total amount of slots are", slotTotal);

            var eventIdArr = [];

            for (let j = 0; j < numEvents; j++) {
                var eventId = 0;
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

            if (numEvents > slotTotal) {
                alert("Not enough slots available in timetable. Please enter events manually!");
            } else {
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

                let today = new Date();
                let targetDay = (key - today.getDay() + 7) % 7; // Calculate the number of days until the next occurrence
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
                var eventData = {
                    title: course,
                    start: dayFormatted,
                    daysOfWeek: [key.toString()],
                    startTime: startT,
                    endTime: endT,
                    color: '#00FF00',
                    recurring: true,
                    id: eventId[i]
                };
                console.log("EventDATA", eventData);
                console.log("checking", eventsInDay);
                eventsInDay[key].shift();
                var temp = await saveEvent(eventData, user);
            }
            setTimeout(5000);
            window.location.href = './timetable.php';
        }




        function saveEvent(eventData, user) {
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

        /*
        function addToCalendar(){
            if (hours > maxHours){
                var r = hours%maxHours;
                var q = Math.floor(hours / maxHours);
            }else{

            }
        }

        function loadNumberOfEventsByDay(events){
            console.log(events);
            for(var i=0; i<events.length; i++){
                var num  = getDayOfWeek(events[i]["start"]);
                eventsByDay[num] = eventsByDay[num] + parseInt(num);
                console.log("test", eventsByDay[num]);
            }
        }

        function findAvailableTime(){
            if (dow == 1){

            }
            for (var i in total_events){
                
            }
        }

        function getDayOfWeek(dateString) {
            // Create a new Date object from the provided dateString
            var date = new Date(dateString);
            // Get the day of the week (0=Sunday, 1=Monday, ..., 6=Saturday)
            var dayOfWeek = date.getDay();
            // Return an array with the day of the week
            return dayOfWeek.toString();
        }

        function getTimeFromDate(dateTimeString) {
            var date = new Date(dateTimeString);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            return hours + ':' + (minutes < 10 ? '0' : '') + minutes;
        }
*/
        /*
                function getEventId() {
                    fetch('http://localhost:3000/api/EventIds', {
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

                        var eventId = Math.round(Math.random() * 100000);
                        while (existingIds.includes(eventId)){
                            eventId = Math.round(Math.random() * 100000);
                        } 
                        // Call the callback with the generated event ID
                        console.log("This", eventId);
                        return eventId;
                    })
                    .catch(error => {
                        console.error('Error fetching existing event IDs:', error);
                        // Call the callback with null to indicate error
                        callback(null);
                    });
                }*/
        /*
        function saveEvent(eventData) {
            console.log(typeof(eventData));
            fetch('http://localhost:3000/api/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify([{userID: 1}, {eventID: eventData.id}, { info: eventData }]) // Wrap eventData in an object with 'info' property
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to save event');
                }
                console.log("Event Addedd Successfully")
                return response.json();
            })
            .then(data => console.log(data.message))
            .catch(error => console.error('Error saving event:', error));
        }*/
    </script>
</body>

</html>
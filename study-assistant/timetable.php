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
    <title>Timetable</title>
    <link href="./css/timetable-style.css" rel="stylesheet" />
    <link href="./css/styles.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler/index.global.min.js"></script>
    <?php include './includes/bootstrap.php'; ?>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js"></script>
    <style>
        /* Calendar styles */
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Color preview */
        #color-preview {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-left: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #008080;
        }
    </style>
</head>

<body>
    <!-- Include page header -->
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container">
        <div id="custom-header">
            <!-- Page title -->
            <h1>My Timetable</h1>
        </div>
        <div id="add-event-form">
            <!-- Event color picker -->
            <label for="event-color">Color:</label>
            <select id="event-color">
                <option value="#008080">Teal</option>
                <option value="#00A36C">Green</option>
                <option value="#6082B6">Blue</option>
                <option value="#900C3F">Red</option>
                <option value="#581845">Purple</option>
                <option value="#C3C200">Yellow</option>

            </select>
            <div id="color-preview"></div>
        </div>
        <div id="calendar"></div>
    </div>
    <!-- Include page footer -->
    <?php include './includes/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                slotDuration: '00:30:00',
                slotLabelInterval: '00:30',
                editable: true, // Enable editing events
                selectable: true, // Enable selecting time slots to add new events
                eventDidMount: function(info) {
                    // Initialize tooltip for each event element
                    tippy(info.el, {
                        content: info.event.title, // Set tooltip content to event title
                        placement: 'top', // Position tooltip at the top of the event
                        trigger: 'mouseenter', // Show tooltip on mouse enter
                    });
                },
                events: [

                    // Default events
                ],
                headerToolbar: {
                    left: '',
                    center: '', // Default title 
                    right: 'timeGridWeek,timeGridDay'
                },
                // Display only the full weekday name
                dayHeaderFormat: {
                    weekday: 'long'
                },
                eventDidMount: function(info) {
                    // Event listener to each event element for right-click removal
                    info.el.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        if (confirm('Are you sure you want to remove this event?')) {
                            // Remove event from the database
                            eventRemove(info.event.id);
                            info.event.remove();
                        }
                    });
                },
                eventDrop: function(info) {
                    // Functionality for when an event is dragged and dropped to a new time slot
                    var eventData = {
                        title: info.event.title,
                        start: info.event.start,
                        color: info.event.backgroundColor,
                        daysOfWeek: getDayOfWeek(info.event.startStr),
                        startTime: getTimeFromDate(info.event.startStr),
                        endTime: getTimeFromDate(info.event.endStr),
                        recurring: true,
                        id: info.event.id
                    };
                    // Update event in the database
                    updateEvent(eventData);
                },
                eventResize: function(info) {
                    // Functionality for when an event's duration is resized
                    var eventData = {
                        title: info.event.title,
                        start: info.event.start,
                        color: info.event.backgroundColor,
                        daysOfWeek: getDayOfWeek(info.event.startStr),
                        startTime: getTimeFromDate(info.event.startStr),
                        endTime: getTimeFromDate(info.event.endStr),
                        recurring: true,
                        id: info.event.id
                    };
                    // Update event in the database
                    updateEvent(eventData);
                },
                eventClick: function(info) {
                    // Functionality for when an event is left clicked
                    var title = prompt('Enter a new title for this event:', info.event.title);
                    if (title) {
                        info.event.setProp('title', title);
                        var eventData = {
                            title: title,
                            start: info.event.start,
                            color: info.event.backgroundColor,
                            daysOfWeek: getDayOfWeek(info.event.startStr),
                            startTime: getTimeFromDate(info.event.startStr),
                            endTime: getTimeFromDate(info.event.endStr),
                            recurring: true,
                            id: info.event.id
                        };
                        // Update event in the database
                        updateEvent(eventData);
                    }
                },
                select: function(info) {
                    // Functionality for when a slot is selected
                    var title = prompt('Enter a title for your event:');
                    var color = document.getElementById('event-color').value;
                    if (title) {
                        getEventId(function(eventId) {
                            if (eventId) {
                                var eventData = {
                                    title: title,
                                    start: info.startStr,
                                    daysOfWeek: getDayOfWeek(info.startStr),
                                    startTime: getTimeFromDate(info.startStr),
                                    endTime: getTimeFromDate(info.endStr),
                                    color: color,
                                    recurring: true,
                                    id: eventId
                                };
                                calendar.addEvent(eventData);
                                // Store event in the database
                                saveEvent(eventData);
                            }
                        });
                        calendar.unselect();
                    }
                }
            });

            var colorSelect = document.getElementById('event-color');
            var colorPreview = document.getElementById('color-preview');
            colorSelect.addEventListener('change', function() {
                // Update the color preview box based on the selected color
                colorPreview.style.backgroundColor = colorSelect.value;
            });

            // Function to get the day of the week number
            function getDayOfWeek(dateString) {
                var date = new Date(dateString);
                var dayOfWeek = date.getDay();
                return [dayOfWeek.toString()];
            }

            // Functuion to get the time from a date string
            function getTimeFromDate(dateTimeString) {
                var date = new Date(dateTimeString);
                var hours = date.getHours();
                var minutes = date.getMinutes();
                return hours + ':' + (minutes < 10 ? '0' : '') + minutes;
            }


            // Function to get all event IDs to see which ones are available to use. 
            function getEventId(callback) {
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
                        while (existingIds.includes(eventId)) {
                            eventId = Math.round(Math.random() * 100000);
                        }
                        // Call the callback with the generated event ID
                        console.log("This", eventId);
                        callback(eventId);
                    })
                    .catch(error => {
                        console.error('Error fetching existing event IDs:', error);
                        // Call the callback with null to indicate error
                        callback(null);
                    });
            }


            // Function to save events
            function saveEvent(eventData) {
                console.log(typeof(eventData));
                // Call endpoint from node.js api
                fetch('http://localhost:3000/api/events', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify([{
                            userID: <?= htmlspecialchars($_SESSION["id"]); ?>
                        }, {
                            eventID: eventData.id
                        }, {
                            info: eventData
                        }]) //Send data to api
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
            }

            // Function to update events
            function updateEvent(eventData) {
                // Call endpoint from node.js api
                fetch('http://localhost:3000/api/updateevents', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify([{
                            userID: <?= htmlspecialchars($_SESSION["id"]); ?>
                        }, {
                            eventID: eventData.id
                        }, {
                            info: eventData
                        }]) // Send updated information to api
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to save event');
                        }
                        console.log("Event Updated Successfully");
                        return response.json();
                    })
                    .then(data => console.log(data.message))
                    .catch(error => console.error('Error saving event:', error));
            }

            // Functiom to remove events
            function eventRemove(id) {
                // Call endpoint from node.js api
                fetch('http://localhost:3000/api/removeevents', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            eventID: id
                        })
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

            // Getting all the events using the node.js api in order to render the timetable
            fetch('http://localhost:3000/api/events')
                .then(response => response.json())
                .then(events => {
                    if (events[0] != undefined) {
                        for (let x in events) {
                            if (events[x]["userID"] == <?= htmlspecialchars($_SESSION["id"]); ?>) {
                                const infoObject = JSON.parse(events[x].info);
                                calendar.addEvent(infoObject);
                            }
                        }
                    }
                    console.log("Events Loaded Successfully");
                })
                .catch(error => console.error('Error fetching events:', error));
            calendar.render();

            var dayHeaders = document.querySelectorAll('.fc-day-header');
            dayHeaders.forEach(function(header) {
                header.style.textDecoration = 'none';
                header.style.cursor = 'default';
                header.addEventListener('click', function(event) {
                    event.preventDefault();
                });
            });
        });
    </script>
</body>
<!-- Include notifications -->
<?php include './includes/notifications.php'; ?>

</html>
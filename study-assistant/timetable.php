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
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        #color-preview {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-left: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #FF0000;
        }
    </style>
</head>

<body>
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container">
        <div id="custom-header">
            <h1>My Timetable</h1>
        </div>
        <div id="add-event-form">
            <label for="event-color">Color:</label>
            <select id="event-color">
                <option value="#FF0000">Red</option>
                <option value="#00FF00">Green</option>
                <option value="#0000FF">Blue</option>
                <!-- Add more color options as needed -->
            </select>
            <div id="color-preview"></div>
        </div>
        <div id="calendar"></div>
    </div>

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

                    // Add more events with different colors as needed
                ],
                headerToolbar: {
                    left: '',
                    center: '', // Default title (can be replaced)
                    right: 'timeGridWeek,timeGridDay'
                },
                // Customize the day header format
                dayHeaderFormat: {
                    weekday: 'long'
                }, // Display only the full weekday name
                eventDidMount: function(info) {
                    // Attach event listener to each event element for right-click removal
                    info.el.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        if (confirm('Are you sure you want to remove this event?')) {
                            eventRemove(info.event.id);
                            info.event.remove();
                            // Remove event from the database (implement backend)
                        }
                    });
                },
                eventDrop: function(info) {
                    // When an event is dragged and dropped to a new time slot
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
                    updateEvent(eventData);
                },
                eventResize: function(info) {
                    // When an event's duration is resized
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
                    updateEvent(eventData);
                },
                eventClick: function(info) {
                    // Handle event click (edit event)
                    var title = prompt('Enter a new title for this event:', info.event.title);
                    if (title) {
                        info.event.setProp('title', title);
                        // Update event in the database (implement backend)
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
                        updateEvent(eventData);
                    }
                },
                select: function(info) {
                    // Handle slot select (add new event)
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

            function getDayOfWeek(dateString) {
                // Create a new Date object from the provided dateString
                var date = new Date(dateString);
                // Get the day of the week (0=Sunday, 1=Monday, ..., 6=Saturday)
                var dayOfWeek = date.getDay();
                // Return an array with the day of the week
                return [dayOfWeek.toString()];
            }

            function getTimeFromDate(dateTimeString) {
                var date = new Date(dateTimeString);
                var hours = date.getHours();
                var minutes = date.getMinutes();
                return hours + ':' + (minutes < 10 ? '0' : '') + minutes;
            }


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


            function saveEvent(eventData) {
                console.log(typeof(eventData));
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
                        }]) // Wrap eventData in an object with 'info' property
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

            function updateEvent(eventData) {
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
                        }]) // Wrap eventData in an object with 'info' property
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

            function eventRemove(id) {
                fetch('http://localhost:3000/api/removeevents', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            eventID: id
                        }) // Wrap eventData in an object with 'info' property
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

</html>
<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='../login.php';</script>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler/index.global.min.js"></script>
    <!--<script src="server.js"></script>-->
    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include './includes/header.php'; ?>
    <div class="container">
        <div id="custom-header">My Timetable</div>
        <div id="add-event-form">
            <label for="event-color">Color:</label>
            <input type="color" id="event-color">
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
                events: [
                    
                    // Add more events with different colors as needed
                ],
                headerToolbar: {
                    left: '',
                    center: '', // Default title (can be replaced)
                    right: 'timeGridWeek,timeGridDay'
                },
                // Customize the day header format
                dayHeaderFormat: { weekday: 'long' }, // Display only the full weekday name
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
                    while (existingIds.includes(eventId)){
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
                    body: JSON.stringify([{userID: <?= htmlspecialchars($_SESSION["id"]); ?>}, {eventID: eventData.id}, { info: eventData }]) // Wrap eventData in an object with 'info' property
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
                    body: JSON.stringify([{userID: <?= htmlspecialchars($_SESSION["id"]); ?>}, {eventID: eventData.id}, { info: eventData }]) // Wrap eventData in an object with 'info' property
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
                    body: JSON.stringify({eventID: id}) // Wrap eventData in an object with 'info' property
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
                if (events[0] != undefined){
                    for (let x in events){
                        if (events[x]["userID"] == <?= htmlspecialchars($_SESSION["id"]); ?>){
                            const infoObject = JSON.parse(events[x].info);
                            calendar.addEvent(infoObject);
                        }
                    }
                }
                console.log("Events Loaded Successfully");
            })
            .catch(error => console.error('Error fetching events:', error));
            calendar.render(); 
        });
    

    </script>
</body>
</html>
const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const cors = require('cors'); // Import the cors middleware
const cron = require('node-cron');

const app = express();
const port = 3000;

// Use the cors middleware
app.use(cors());

// Create MySQL connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'timetable'
});

// Connect to MySQL
db.connect((err) => {
    if (err) {
        throw err;
    }
    console.log('Connected to MySQL database');
});

// Middleware
app.use(bodyParser.json());




// Setup cron job to run every minute
cron.schedule('* * * * *', () => {
    fetchNotifications();
});


// Endpoint to fetch notifications
function fetchNotifications(userID, callback) {
    // const userID = 10; // Assuming userID is hardcoded for now
    const query = "SELECT JSON_EXTRACT(info, '$.title') AS title, JSON_EXTRACT(info, '$.daysOfWeek') AS dow, JSON_EXTRACT(info, '$.startTime') AS start, JSON_EXTRACT(info, '$.endTime') AS end FROM events WHERE userID = ?";

    // Execute the query
    db.query(query, [userID], (err, results) => {
        if (err) {
            console.error('Error executing MySQL query:', err);
            callback(err, null);
            return;
        }

        try {
            const currentDate = new Date();
            const currentTimeMinutes = currentDate.getHours() * 60 + currentDate.getMinutes(); // Current time in minutes since midnight
            const currentTimeUpper = currentTimeMinutes + 1;
            const currentTimeLower = currentTimeMinutes - 1;
            const currentDay = currentDate.getDay(); // 0 (Sunday) to 6 (Saturday)

            for (let j = 0; j < results.length; j++) {
                const startParts = results[j].start.split(':').map(part => parseInt(part.replace(/"/g, '')));

                // Extract end time parts and remove double quotes
                const endParts = results[j].end.split(':').map(part => parseInt(part.replace(/"/g, '')));


                // Calculate event start and end times in minutes
                const eventStart = startParts[0] * 60 + startParts[1];
                const eventEnd = endParts[0] * 60 + endParts[1];

                // Check if the event is upcoming based on both day of the week and time
                if (parseInt(results[j].dow[2]) === currentDay && eventStart >= currentTimeLower && eventStart <= currentTimeUpper) {
                    const notifications = `Event ${results[j].title} is happening now!`;
                    console.log(notifications);
                    callback(null, notifications);
                    break;
                }
            }

        } catch (error) {
            console.error('Error processing events:', error);
            callback(error, null);
        }
    });

}


// Endpoint to fetch notifications
app.get('/api/notifications/:userId', (req, res) => {
    const userID = req.params.userId;
    
    // Call the fetchNotifications function
    fetchNotifications(userID, (err, notifications) => {
        if (err) {
            res.status(500).send({ error: 'Error retrieving notifications from database' });
        } else {
            res.json(notifications);
        }
    });
});


// Serve the frontend HTML file
app.use(express.static('public'));




function fetchExistingEventIds(callback) {
    const sql = 'SELECT eventID FROM events';
    db.query(sql, (err, result) => {
        if (err) {
            console.error('Error fetching eventIDs:', err);
            callback(err, null);
        } else {
            const existingIds = result.map(row => row.eventID);
            console.log("Result", existingIds);
            callback(null, existingIds);
        }
    });
}


// Route to fetch existing event IDs
app.get('/api/EventIds', function(req, res) {
    fetchExistingEventIds((err, existingIds) => {
        if (err) {
            console.error('Error fetching existing event IDs:', err);
            res.status(500).send({ error: 'Error fetching existing event IDs from database' });
        } else {
            res.json(existingIds);
        }
    });
});



function convertToTime(minutes) {
    // Calculate hours and minutes
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;

    // Determine AM or PM
    const period = hours < 12 ? 'AM' : 'PM';

    // Convert hours to 12-hour format
    const displayHours = hours % 12 || 12;

    // Pad minutes with leading zero if necessary
    const displayMins = String(mins).padStart(2, '0');

    // Format time string
    const timeString = `${displayHours}:${displayMins} ${period}`;

    return timeString;
}



app.get('/api/upcoming_events/:userId', function(req, res) {
    const userID = req.params.userId;
    // const userID = 10; // Assuming userID is hardcoded for now
    const query = "SELECT JSON_EXTRACT(info, '$.title') AS title, JSON_EXTRACT(info, '$.daysOfWeek') AS dow, JSON_EXTRACT(info, '$.startTime') AS start, JSON_EXTRACT(info, '$.endTime') AS end FROM events WHERE userID = ?";

    // Execute the query
    db.query(query, [userID], (err, results1) => {
        if (err) {
            console.error('Error executing MySQL query:', err);
            res.status(500).send({ error: 'Error retrieving events from database' });
            return;
        }

        try {
            const currentDate = new Date();
            const currentTimeMinutes = currentDate.getHours() * 60 + currentDate.getMinutes(); // Current time in minutes since midnight
            const currentDay = currentDate.getDay(); // 0 (Sunday) to 6 (Saturday)
            const daysOfWeek = [];

            for (let i = 0; i < 7; i++) {
                daysOfWeek.push((currentDay + i) % 7);
            }
            console.log(daysOfWeek)

            const upcomingEvents = [];
            const upcomingSDEvents = [];
            const results = [];


            for (let i = 0; i < daysOfWeek.length; i++) {
                for (let j = 0; j < results1.length; j++) {
                    if (daysOfWeek[i] == parseInt(results1[j].dow[2])){
                        results.push(results1[j])
                        console.log(results1[j].title, parseInt(results1[j].dow[2]), results1[j].start)
                    }
                }
            }

            var check = false;

            for (let i = 0; i < daysOfWeek.length; i++) {
                for (let j = 0; j < results.length; j++) {
                    if (daysOfWeek[i] === parseInt(results[j].dow[2])) {
                        // Extract start and end times and remove double quotes
                        const startParts = results[j].start.split(':').map(part => parseInt(part.replace(/"/g, '')));

                        // Extract end time parts and remove double quotes
                        const endParts = results[j].end.split(':').map(part => parseInt(part.replace(/"/g, '')));


                        // Calculate event start and end times in minutes
                        const eventStart = startParts[0] * 60 + startParts[1];
                        const eventEnd = endParts[0] * 60 + endParts[1];

                        // Check if the event is upcoming based on both day of the week and time
                        if (daysOfWeek[i] === currentDay && eventStart >= currentTimeMinutes) {
                            upcomingEvents.push({
                                title: results[j].title,
                                dayOfWeek: daysOfWeek[i],
                                startTime: convertToTime(eventStart),
                                endTime: convertToTime(eventEnd)
                            });

                            
                            // Break the loop if three upcoming events are found
                            if (upcomingEvents.length >= 3 && parseInt(results[j].dow[2]) != parseInt(results[j+1].dow[2])) {
                                check = true;
                                upcomingEvents.sort((a, b) => {
                                    if (a.dayOfWeek === b.dayOfWeek) {
                                        return a.startTime - b.startTime;
                                    } else {
                                        return 0; // Maintain the order if dayOfWeek does not match
                                    }
                                });
                                break;
                            }
                        } else if (daysOfWeek[i] > currentDay || daysOfWeek[i] < currentDay) {
                            // If it's a future day, include all events on that day
                            upcomingEvents.push({
                                title: results[j].title,
                                dayOfWeek: daysOfWeek[i],
                                startTime: convertToTime(eventStart),
                                endTime: convertToTime(eventEnd)
                            });

                            console.log(upcomingEvents.length, parseInt(results[j].dow[2]), parseInt(results[j+1].dow[2]))
                            // Break the loop if three upcoming events are found
                            if (upcomingEvents.length >= 3 && parseInt(results[j].dow[2]) != parseInt(results[j+1].dow[2])) {
                                check = true;
                                upcomingEvents.sort((a, b) => {
                                    if (a.dayOfWeek === b.dayOfWeek) {
                                        return a.startTime - b.startTime;
                                    } else {
                                        return 0; // Maintain the order if dayOfWeek does not match
                                    }
                                });
                                break;
                            }
                        } else if (daysOfWeek[i] === currentDay && eventStart < currentTimeMinutes) {
                            // If it's a future day, include all events on that day
                            upcomingSDEvents.push({
                                title: results[j].title,
                                dayOfWeek: daysOfWeek[i],
                                startTime: convertToTime(eventStart),
                                endTime: convertToTime(eventEnd)
                            });
                            upcomingSDEvents.sort((a, b) => a.startTime - b.startTime);
                        }
                    }
                }
                // Sort events by start time regardless of the day of the week
                


                const temp = upcomingEvents.length;
                // Break the loop if three upcoming events are found
                if ((upcomingEvents.length >= 3 && parseInt(results[temp-1].dow[2]) != parseInt(results[temp].dow[2])) || check) {
                    upcomingEvents.sort((a, b) => {
                        if (a.dayOfWeek === b.dayOfWeek) {
                            return a.startTime - b.startTime;
                        } else {
                            return 0; // Maintain the order if dayOfWeek does not match
                        }
                    });
                    break;
                }
            }
            if (upcomingEvents.length < 3){
                for (let x = 0; x < daysOfWeek.length; x++){
                    upcomingEvents.push(upcomingEvents[x]);
                }
            }

            // Print or return the upcoming events
            console.log('Upcoming Events:', upcomingEvents);
            console.log('Check', upcomingSDEvents);
            res.status(200).send(upcomingEvents);
        } catch (error) {
            console.error('Error processing events:', error);
            res.status(500).send({ error: 'Error processing events' });
        }
    });

});





// Route to handle POST requests to save events
app.post('/api/events', (req, res) => {
    const items = req.body;
    userID = items[0].userID;
    eventID = items[1].eventID;
    info = items[2].info;
    console.log("User", userID);
    console.log("Event", eventID);
    console.log(info);
    if (!info) {
        return res.status(400).send({ error: 'Event data is required' });
    }
    const sql = 'INSERT INTO events (userID, eventID, info) VALUES (?, ?, ?)';
    db.query(sql, [userID, eventID, JSON.stringify(info)], (err, result) => { // Stringify the 'info' object
        if (err) {
            console.error('Error saving event:', err);
            res.status(500).send({ error: 'Error saving event to database' });
        } else {
            res.status(201).send({ message: 'Event Saved Successfully' });
        }
    });
});



app.post('/api/updateevents', (req, res) => {
    const items = req.body;
    userID = items[0].userID;
    eventID = items[1].eventID;
    info = items[2].info;
    console.log(userID);
    console.log(eventID);
    console.log(info);
    if (!info) {
        return res.status(400).send({ error: 'Event data is required' });
    }
    const sql = 'UPDATE events SET userId = ?, eventID = ?, info = ? WHERE eventID = ?';
    db.query(sql, [userID, eventID, JSON.stringify(info), eventID], (err, result) => { // Stringify the 'info' object
        if (err) {
            console.error('Error saving event:', err);
            res.status(500).send({ error: 'Error saving event to database' });
        } else {
            res.status(201).send({ message: 'Event Updated Successfully' });
        }
    });
});



app.post('/api/removeevents', (req, res) => {
    const eventID = req.body.eventID;
    console.log(eventID);
    if (!eventID) {
        return res.status(400).send({ error: 'Event data is required' });
    }
    const sql = 'DELETE FROM events WHERE eventID = ?';
    db.query(sql, [eventID], (err, result) => { // Stringify the 'info' object
        if (err) {
            console.error('Error saving event:', err);
            res.status(500).send({ error: 'Error saving event to database' });
        } else {
            res.status(201).send({ message: 'Event Deleted Successfully' });
        }
    });
});





app.get('/api/events', (req, res) => {
    const sql = 'SELECT * FROM events';
    db.query(sql, (err, result) => {
        if (err) {
            res.status(500).send({ error: 'Error retrieving events from database' });
        } else {
            res.status(200).send(result);
        }
    });
});


// Start server
app.listen(port, () => {
    console.log(`Server running on port ${port}`);
});

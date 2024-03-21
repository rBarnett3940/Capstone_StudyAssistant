const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const cors = require('cors'); // Import the cors middleware

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

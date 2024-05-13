<?php
// Start the session
session_start();

// Check if the course code and title are received via POST
if (isset($_POST['course_code']) && isset($_POST['course_title'])) {
    // Store the received data in session variables
    $_SESSION['course_code'] = $_POST['course_code'];
    $_SESSION['course_title'] = $_POST['course_title'];
    // Respond with a success message
    echo 'Session variables set successfully.';
}
if (isset($_POST['message'])) {
    // Store the message in a session variable
    $_SESSION['generated_hours'] = $_POST['message'];
    echo json_encode(array("status" => "success"));
} else {
    // Respond with an error message if the data is not received
    echo 'Error: Course code and title not received.';
}

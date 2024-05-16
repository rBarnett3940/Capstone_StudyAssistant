<?php
// Start the session
session_start();

// Check if the course code and title are received via POST
if (isset($_POST['course_code']) && isset($_POST['course_title'])) {
    // Store course codee and title in session variables
    $_SESSION['course_code'] = $_POST['course_code'];
    $_SESSION['course_title'] = $_POST['course_title'];
    // Respond with a success message
    echo 'Session variables set successfully.';
    // Check if massage was received
} elseif (isset($_POST['message'])) {
    // Store the message and prediction in a session variable
    $_SESSION['message'] = $_POST['message'];
    $_SESSION['prediction'] = $_POST['prediction'];
    // Respond with a success message
    echo json_encode(array("status" => "success"));
    exit;
} else {
    // Respond with an error message if the data is not received
    echo 'Error: Course code and title not received.';
}

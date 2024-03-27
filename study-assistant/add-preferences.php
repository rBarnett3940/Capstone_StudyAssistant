<?php
    # Initialize the session
    session_start();

    # If user is not logged in then redirect him to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
        echo "<script>window.location.href='./login.php';</script>";
        exit;
    }

    # Include config file
    include_once './includes/config-registered-db.php';

    # Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        # Retrieve form data
        $mode = $_POST["mode"];
        $tod = $_POST["tod"];
        $dow = $_POST["dow"];
        $env = $_POST["env"];
        $tech = $_POST["tech"];
        $retention = $_POST["retention"];
        $maxHours = $_POST["maxHours"];

        $_SESSION["mode"] = $mode;
        $_SESSION["tod"] = $tod;
        $_SESSION["dow"] = $dow;
        $_SESSION["env"] = $env;
        $_SESSION["tech"] = $tech;
        $_SESSION["retention"] = $retention;
        $_SESSION["maxHours"] = $maxHours;
        
        # Check if preferences already exist for the user
        $sql_check = "SELECT * FROM preferences WHERE userID = ?";
        $stmt_check = $link->prepare($sql_check);
        $stmt_check->bind_param("i", $_SESSION["id"]);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            # Preferences exist, update them
            $sql_update = "UPDATE preferences SET mode=?, tod=?, dow=?, env=?, tech=?, retain=?, maxHours=? WHERE userID=?";
            $stmt_update = $link->prepare($sql_update);
            $stmt_update->bind_param("iiiiiiii", $mode, $tod, $dow, $env, $tech, $retention, $maxHours, $_SESSION["id"]);
            $stmt_update->execute();
            $message = "Preferences updated successfully!";
        } else {
            # Preferences don't exist, insert new preferences
            $sql_insert = "INSERT INTO preferences (userID, mode, tod, dow, env, tech, retain, maxHours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $link->prepare($sql_insert);
            $stmt_insert->bind_param("iiiiiiii", $_SESSION["id"], $mode, $tod, $dow, $env, $tech, $retention, $maxHours);
            $stmt_insert->execute();
            $message = "Preferences added successfully!";
        }
        $stmt_check->close();
    }

    # Retrieve existing preferences if any
    $default_values = [];
    $sql_select = "SELECT * FROM preferences WHERE userID = ?";
    $stmt_select = $link->prepare($sql_select);
    $stmt_select->bind_param("i", $_SESSION["id"]);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    if ($result_select->num_rows > 0) {
        $row = $result_select->fetch_assoc();
        $default_values = $row;
    }
    $stmt_select->close();
?>


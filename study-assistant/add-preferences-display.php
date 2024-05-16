<!-- User prefernces -->
<?php
# Initialize the session
session_start();

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>window.location.href='./login.php';</script>";
    exit;
}

if (isset($_SESSION['mode'])) {
    echo "<script>window.location.href='./timetable.php';</script>";
    exit;
}

// Include Backend
include "./add-preferences.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <?php include './includes/bootstrap.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <title>Add Preferences</title>
</head>

<body>
    <!-- Include page header -->
    <?php include './includes/header.php'; ?>
    <br>
    <div class="container course">
        <h1>Add Preferences</h1>
        <!-- Check for update or added message from backend -->
        <?php if (isset($message) && !empty($message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <!-- Form for prefernces -->
        <form id="prediction-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="itms">
                <!-- Mode -->
                <label for="mode">Mode: <span>(Which mode do you prefer to study in?)</span></label>
                <br>
                <select id="mode" name="mode" required>
                    <option value="" disabled selected hidden>Choose Mode...</option>
                    <option value="1" <?php if (isset($default_values['mode']) && $default_values['mode'] == 1) echo "selected"; ?>>Alone</option>
                    <option value="2" <?php if (isset($default_values['mode']) && $default_values['mode'] == 2) echo "selected"; ?>>In a group</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <!-- Time of Day -->
                <label for="tod">Time of Day: <span>(Do you have a preferred time of day for studying?)</span></label>
                <br>
                <select id="tod" name="tod" required>
                    <option value="" disabled selected hidden>Choose Time of Day...</option>
                    <option value="1" <?php if (isset($default_values['tod']) && $default_values['tod'] == 1) echo "selected"; ?>>Morning</option>
                    <option value="2" <?php if (isset($default_values['tod']) && $default_values['tod'] == 2) echo "selected"; ?>>Afternoon</option>
                    <option value="3" <?php if (isset($default_values['tod']) && $default_values['tod'] == 3) echo "selected"; ?>>Evening</option>
                    <option value="4" <?php if (isset($default_values['tod']) && $default_values['tod'] == 4) echo "selected"; ?>>No Preference</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <!-- Time of Week -->
                <label for="dow">Time of Week: <span>(Do you have a preferred time of the week for studying?)</span></label>
                <br>
                <select id="dow" name="dow" required>
                    <option value="" disabled selected hidden>Choose Time of the Week...</option>
                    <option value="1" <?php if (isset($default_values['dow']) && $default_values['dow'] == 1) echo "selected"; ?>>Weekdays</option>
                    <option value="2" <?php if (isset($default_values['dow']) && $default_values['dow'] == 2) echo "selected"; ?>>Weekends</option>
                    <option value="3" <?php if (isset($default_values['dow']) && $default_values['dow'] == 3) echo "selected"; ?>>No Preference</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <!-- Environment -->
                <label for="env">Environment: <span>(Do you prefer studying in a quiet environment or with background noise?)</span></label>
                <br>
                <select id="env" name="env" required>
                    <option value="" disabled selected hidden>Choose Environment...</option>
                    <option value="1" <?php if (isset($default_values['env']) && $default_values['env'] == 1) echo "selected"; ?>>Quiet Environment</option>
                    <option value="2" <?php if (isset($default_values['env']) && $default_values['env'] == 2) echo "selected"; ?>>Background Noise</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <!-- Study Aids/Techniques -->
                <label for="tech">Study Aid/Technique: <span>(Do you use any study aids or techniques?)</span></label>
                <br>
                <select id="tech" name="tech" required>
                    <option value="" disabled selected hidden>Choose Study Aid/Technique...</option>
                    <option value="1" <?php if (isset($default_values['tech']) && $default_values['tech'] == 1) echo "selected"; ?>>Flashcards</option>
                    <option value="2" <?php if (isset($default_values['tech']) && $default_values['tech'] == 2) echo "selected"; ?>>Study Groups</option>
                    <option value="3" <?php if (isset($default_values['tech']) && $default_values['tech'] == 3) echo "selected"; ?>>Online Resources</option>
                    <option value="4" <?php if (isset($default_values['tech']) && $default_values['tech'] == 4) echo "selected"; ?>>No</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <!-- Retention -->
                <label for="retention">Retention: <span>(How well do you retain information?)</span></label>
                <br>
                <select id="retention" name="retention" required>
                    <option value="" disabled selected hidden>Choose Retention...</option>
                    <option value="4" <?php if (isset($default_values['retain']) && $default_values['retain'] == 4) echo "selected"; ?>>Excellent</option>
                    <option value="3" <?php if (isset($default_values['retain']) && $default_values['retain'] == 3) echo "selected"; ?>>Good</option>
                    <option value="2" <?php if (isset($default_values['retain']) && $default_values['retain'] == 2) echo "selected"; ?>>Fair</option>
                    <option value="1" <?php if (isset($default_values['retain']) && $default_values['retain'] == 1) echo "selected"; ?>>Poor</option>
                </select>
            </div>
            <br>
            <div class="itms">
                <!-- Max-Hours -->
                <label for="maxHours">Max Study Hours: <span>(How many hours at a time do you usually study for before taking a break?)</span></label>
                <br>
                <select id="maxHours" name="maxHours" required>
                    <option value="" disabled selected hidden>Choose Max Study Hours...</option>
                    <option value="1" <?php if (isset($default_values['maxHours']) && $default_values['maxHours'] == 1) echo "selected"; ?>>1</option>
                    <option value="2" <?php if (isset($default_values['maxHours']) && $default_values['maxHours'] == 2) echo "selected"; ?>>2</option>
                    <option value="3" <?php if (isset($default_values['maxHours']) && $default_values['maxHours'] == 3) echo "selected"; ?>>3</option>
                    <option value="4" <?php if (isset($default_values['maxHours']) && $default_values['maxHours'] == 4) echo "selected"; ?>>4</option>
                    <option value="5" <?php if (isset($default_values['maxHours']) && $default_values['maxHours'] == 5) echo "selected"; ?>>5</option>
                </select>
            </div>
            <br>
            <!-- Submit Button -->
            <button type="submit" id="generate-btn" class="btn btn-primary">Add Preferences</button>
        </form>
    </div>
    <!-- Include page footer -->
    <?php include './includes/footer.php'; ?>
</body>
<!-- Include notifications -->
<?php include './includes/notifications.php'; ?>

</html>
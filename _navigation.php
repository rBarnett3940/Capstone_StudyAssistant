<aside class="sidebar">
    <ul>
        <a href="./dashboard.php" <?php if(basename($_SERVER['PHP_SELF']) == "dashboard.php"){echo "class=\"currentPage\"";} ?> >
            <li>
                <i class="material-icons">home</i>
                Home
            </li>
        </a>

        <a href="./applicationProcessing.php" <?php if(basename($_SERVER['PHP_SELF']) == "studySchedule.php"){echo "class=\"currentPage\"";} ?> >
            <li>
                <i class="material-icons">assignment</i>
                Study Schedule
            </li>
        </a>

        <a href="./roomAssignment.php" <?php if(basename($_SERVER['PHP_SELF']) == "reminders.php"){echo "class=\"currentPage\"";} ?>>
            <li>
                <i class="material-icons">hotel</i>
                Reminders & Notifications
            </li>
        </a>

        <a href="./residentProcessing.php" <?php if(basename($_SERVER['PHP_SELF']) == "studyResources.php"){echo "class=\"currentPage\"";} ?>>
            <li>
                <i class="material-icons">people_outline</i>
                Study Resources
            </li>
        </a>

        <a href="./requestAddForm.php" <?php if(basename($_SERVER['PHP_SELF']) == "userSettings.php"){echo "class=\"currentPage\"";} ?>>
            <li>
                <i class="material-icons">build</i>
                User Settings
            </li>
        </a>

        <hr>
        <a href="./logout.php" <?php if(basename($_SERVER['PHP_SELF']) == "logout.php"){echo "class=\"currentPage\"";} ?>>
            <li>
                <i class="material-icons">exit_to_app</i>
                Logout
            </li>
        </a>
    </ul>
</aside>
<!-- Includes for notifications script -->
<script>
    let userID2 = <?php echo $_SESSION['id']; ?>;
    var urlLink2 = `http://localhost:3000/api/notifications/${userID2}`;

    function checkNotifications() {
        fetch(urlLink2) // Assuming this is the endpoint to fetch notifications
            .then(response => response.json())
            .then(data => {
                // Display notifications to the user
                if (data.length > 0) {
                    alert(data)
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }

    // Call the checkNotifications function periodically
    setInterval(checkNotifications, 30000); // Check every 30 seconds
</script>
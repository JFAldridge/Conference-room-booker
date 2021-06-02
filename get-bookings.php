<?php
// Include the database connection:
include('includes/mysqli_connect.php');

// Get the room parameter from URL and turn to int
$room_id = $_REQUEST["room_id"];
$room_id = intval($room_id);
if ($room_id < 0) {
    print 'error';
    exit();
};

// Define the query
$query = "SELECT name, start_time
    FROM bookings 
    INNER JOIN rooms ON bookings.room_id = rooms.room_id
    INNER JOIN users ON bookings.user_id = users.user_id
    WHERE bookings.room_id = ?
    AND DATE(start_time) >= CURDATE()
    ORDER BY start_time";

// Prepare statement
$stmt = $dbc->prepare($query);
$stmt->bind_param('i', $room_id);

// Run the query:
$stmt->execute();

if ($result = $stmt->get_result()) {
    // Build table header
    print '<table class="table"><tr><th>Booker</th><th>Date</th><th>Time</th></tr>';
    
    // Build rows
    while ($row = mysqli_fetch_array($result)) {

        // Turn query date string into php datetime
        $query_datetime = strtotime($row['start_time']);
        // Make readable date and time
        $booking_date = date('n/j/Y', $query_datetime);
        $booking_time = date('g:i A', $query_datetime);

        print "<tr><td>{$row['name']}</td><td>{$booking_date}</td><td>{$booking_time}</td></tr>";
    }

    // Close table
    print '</table>';

} else { // Query didn't run
    print '<p style="color:red;">Could not retrieve the
    data becaues:<br>' . mysqli_error($dbc) . '.</p><p>
    The query being run wasa: ' . $query . '</p>';
}


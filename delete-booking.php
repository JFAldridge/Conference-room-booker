<?php 
// Include the database connection:
include('includes/mysqli_connect.php');

// Include functions
include_once('includes/functions.php');

session_start();

// Pull out date_picked and room_id for booking table retrieval
$date_picked = $_REQUEST["date_picked"];
$room_id = intval($_REQUEST["room_id"]);
$booking_id = intval($_REQUEST["booking_id"]);


// Define the query
$query = "DELETE FROM bookings 
        WHERE booking_id = ?";

// Prepare statement
$stmt = $dbc->prepare($query);
$stmt->bind_param('i', $booking_id);

// Run the query:
$stmt->execute();

/////// Retrieve bookings table with new booking ////////////

if ($timestamp_picked = strtotime($date_picked)) {
    if (is_weekday($timestamp_picked)) {
        
        $executed_stmt = run_booking_query($dbc, $timestamp_picked, $room_id);

        if ($result = $executed_stmt->get_result()) {
            $booked_hours_arr = build_booked_hours_arr($result);
            print print_day_bookings_table($booked_hours_arr);

        } else { // Query didn't run
            print '<p style="color:red;">Could not retrieve the
            data becaues:<br>' . mysqli_error($dbc) . '.</p><p>
            The query being run was a: ' . $query . '</p>';
        }
    } else {
        print "<p>Rooms can only be booked 8am to 5pm on weekdays.</p>";
    }
}


?>
<?php
// Include the database connection:
include('includes/mysqli_connect.php');

// Start session
session_start();

// Include functions
include('includes/functions.php');

// Get the room parameter from URL and turn to int
$date_picked = $_REQUEST['date_picked'];
$room_id = $_REQUEST["room_id"];
$room_id = intval($room_id);
if ($room_id < 0) {
    print 'error';
    exit();
};

// Get the date parameter from URL and turn to datetime
if ($timestamp_picked = strtotime($date_picked)) {
    if (is_weekday($timestamp_picked)) {
        
        $executed_stmt = run_booking_query($dbc, $timestamp_picked, $room_id);

        if ($result = $executed_stmt->get_result()) {
            $booked_hours_arr = build_booked_hours_arr($result);
            print print_day_bookings_table($booked_hours_arr);

        } else { // Query didn't run
            print '<p style="color:red;">Could not retrieve the
            data becaues:<br>' . mysqli_error($dbc) . '.</p><p>
            The query being run wasa: ' . $query . '</p>';
        }
    } else {
        print '<p class="text-danger">Rooms can only be booked 8am to 5pm on weekdays.</p>';
    }
}


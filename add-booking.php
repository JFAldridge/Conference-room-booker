<?php 
// Include the database connection:
include('includes/mysqli_connect.php');
// Include functions
include_once('includes/functions.php');

session_start();

/////// Insert the Booking ////////////

// Get json data {"hour": hour, "date_picked": date_picked, "room_id": room_id}
$input_json = file_get_contents('php://input');
$input = json_decode($input_json, true);

// Pull out date_picked for booking table retrieval
$date_picked = $input["date_picked"];

// Build query variables
$user_id = intval($_SESSION["user_id"]);
$room_id = intval($input["room_id"]);
$start_time = get_start_time_string($input["date_picked"], $input["hour"]);
$end_time = get_end_time_string($input["date_picked"], $input["hour"]);

// Define the query
$query = "INSERT INTO bookings (user_id, room_id, start_time, end_time)
    VALUES (?, ?, ?, ?)";

// Prepare statement
$stmt = $dbc->prepare($query);
$stmt->bind_param('iiss', $user_id, $room_id, $start_time, $end_time);

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
            The query being run wasa: ' . $query . '</p>';
        }
    } else {
        print "<p>Rooms can only be booked 8am to 5pm on weekdays.</p>";
    }
}


?>
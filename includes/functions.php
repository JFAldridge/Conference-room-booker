<?php
/////// Header //////////
function print_header() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <title>Document</title>
    </head>
    <body>
    <header class="d-flex flex-wrap justify-content-center py-3 mb-5 border-bottom">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
      <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
      <span class="fs-4">Room Booker</span>
    </a>
    <?php if (logged_in()) { ?>
    <ul class="nav nav-pills">
        <li class="nav-item a"><a href="add-room.php" class="nav-link active bg-dark me-5">Add Room</a></li>
        <li class="nav-item b"><a href="logout.php" class="nav-link active bg-dark me-5">Logout</a></li>
    </ul>
    <?php } ?>
  </header>
    <?php
}

////// index.php /////////
/// Sidebar ///
function build_index_sidebar($dbc, &$first_room_arr) {
    // Define the query
    $query = 'SELECT room_id, room_number, building
                FROM rooms
                ORDER BY building, room_number';

    // Run the query:
        if ($result = mysqli_query($dbc, $query)) {
            // Store first room as default for booking area
            $first_room_arr = mysqli_fetch_array($result, MYSQLI_ASSOC);
            // Reset pointer
            mysqli_data_seek($result, 0);

            // These two functions build the left sidebar
            $building_room_arr = build_building_room_array($result);
            display_building_room_list($building_room_arr);
    } else { // Query didn't run
            print '<p style="color:red;">Could not retrieve the
            data becaues:<br>' . mysqli_error($dbc) . '.</p><p>
            The query being run was: ' . $query . '</p>';
    }
}

function build_building_room_array($result_object) {
    // Map query results to a multidimensional array
    $building_room_arr = array();

    while ($row = mysqli_fetch_array($result_object)) {
        $current_building = $row['building'];
        $room_arr = Array(
            "room_number" => $row['room_number'], 
            "room_id" => $row['room_id']
        );

        $building_room_arr[$current_building][] = $room_arr; 
    }

    return $building_room_arr;
}

function display_building_room_list($buildings_and_rooms) {
    // Print rooms underneath the building they belong in:
    print '<h3 class="pb-4">Pick a Room</h3>';
    foreach($buildings_and_rooms as $building => $rooms) {
        print "<h5>{$building}</h5>";

        foreach($rooms as $room_arr) {
            print '<p class="room-options" onclick="updateRoomArea(event)" data-roomid="' . $room_arr['room_id'] . '" data-building="' . $building . '">' . $room_arr['room_number'] . '</p>';
        }
    }
}

/// Room Selection Form ///

function print_room_selection_form($first_room_arr) {
    ?>
    <div class="row">
        <div class="col">
            <h2 id="building-header"><?php print $first_room_arr['building']; ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h4 id="room-number-header"><?php print $first_room_arr['room_number']; ?></h4>
        </div>
    </div>
    <div class="row">
    
    </div>
    <div class="row">
        <div class="col">
            <form action="add_booking.php" id="booking-form" class="row">
                <div class="col-2">
                    <label for="date" class="form-label">Pick a Date</label>
                </div>
                <div class="col-3">
                    <input onchange="updateTimeSelect(this)" type="date" name="date" id="date-picker">
                    <input type="hidden" name="room-id" id="room-id-input" value=<?php print '"' . $first_room_arr['room_id'] . '"' ?>> 
                </div>
                <div class="col-2">
                    <input type="submit" value="Book Timeslot" class="btn btn-dark">
                </div>
            </form>
        </div>
    </div>
    <?php
}

// Filter function
function is_weekday($timestamp) {
    if (date('N', $timestamp) <= 5) {
        return true;
    } else {
        return false;
    }
}

/// Bookings section ///

function run_booking_query($dbc, $timestamp_picked, $room_id) {
    $query = "SELECT name, start_time, bookings.booking_id, users.user_id
            FROM bookings 
            INNER JOIN rooms ON bookings.room_id = rooms.room_id
            INNER JOIN users ON bookings.user_id = users.user_id
            WHERE bookings.room_id = ?
            AND DATE(start_time) = ?
            ORDER BY start_time";

    // Format timestamp
    $sql_date_str =  date('Y-m-d', $timestamp_picked);

    // Prepare statement
    $stmt = $dbc->prepare($query);
    $stmt->bind_param('is', $room_id, $sql_date_str);

    // Run the query:
    $stmt->execute();
    return $stmt;
}

function build_booked_hours_arr($result_object) {
    $booked_hours_arr = [];

    // Iterate through result
    while ($row = mysqli_fetch_array($result_object)) {
        // Turn query date string into php datetime
        $query_datetime = strtotime($row['start_time']);
        // Push to booked hours as (name) => (hour of start time)
        $booked_hours_arr[date('G', $query_datetime)] = ['name' => $row['name'], 'hour' => date('ga', $query_datetime), 'hour_military' => date('G', $query_datetime), 'booking_id' => strval($row['booking_id']), 'user_id' => strval($row['user_id'])];
    }

    return $booked_hours_arr;
}


function print_day_bookings_table($booked_h_arr) {
    // Print table header
    print '<table class="table"><tr><th>Time</th><th>Reserved By</th><th>Modify</th></tr>';
    
    // Build rows
    for ($i = 8; $i <= 17; $i++) {
        if (array_key_exists($i, $booked_h_arr)) {
            $booked_h_row = $booked_h_arr[$i];
            $table_row = "<tr><td>{$booked_h_row['hour']}</td><td>{$booked_h_row['name']}</td>";
            if ($booked_h_row['user_id'] == $_SESSION['user_id']) {
                $table_row .= '<td class="remove-bookings" data-bookingid="' . $booked_h_row['booking_id'] . '">Remove</td></tr>';
            } else {
                $table_row .= "<td></td></tr>";
            }
            print $table_row;
        } else {
            $readable_hour = int_to_hour($i);
            print "<tr><td>{$readable_hour}</td><td class='book-buttons' data-hour='{$i}'>Book Slot</td><td></td></tr>";
        }
    }
    // Close table
    print '</table>';
}

// Conversion Function

function int_to_hour($integer) {
    if ($integer < 12) {
        return strval($integer) . 'am';
    } else if ($integer == 12) {
        return strval($integer) . 'pm';
    } else {
        $integer -= 12;
        return strval($integer) . 'pm';
    }
}

////// Footer /////////

function print_footer() {
    ?>
        <script src="javascript/app.js"></script>
    </body>
    </html>
    <?php
}

// Conversion functions

function get_start_time_string($date, $hour) {
    if (strlen($hour) == 1) {
        $hour = '0' . $hour;
    }

    return $date . " " . $hour . ":00:00";
}

function get_end_time_string($date, $hour) {
    if (strlen($hour) == 1) {
        $hour = '0' . $hour;
    }

    return $date . " " . $hour . ":59:00";
}

function logged_in() {
    return isset($_SESSION["user_id"]);
}
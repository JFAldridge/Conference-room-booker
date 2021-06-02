<?php
// Include functions
include_once('includes/functions.php');

// Start session
session_start();

// Redirect to login if user is not logged in
if (!logged_in()) {
    header('Location: login.php');
    exit;
}

// Include the database connection:
include('includes/mysqli_connect.php');

//// If add-room form was submited /////

if (isset($_POST['room-number']) && isset($_POST['building'])) {
    // Build query variables
    $room_number = intval($_POST['room-number']);
    $building = $_POST['building'];

    // Define the query
    $query = "INSERT INTO rooms (room_number, building)
        VALUES (?, ?)";

    // Prepare statement
    $stmt = $dbc->prepare($query);
    $stmt->bind_param('is', $room_number, $building);

    // Run the query:
    $stmt->execute();

    // Redirect to index.php
    header('Location: index.php');
    exit;
}

// Define the query
$query = "SELECT DISTINCT building
            FROM rooms";

// Prepare statement
$stmt = $dbc->prepare($query);

// Run the query:
$stmt->execute();

print_header();
?>
<div class="container">
    <div class="row">
        <div class="col-3">
        <?php 
        if (isset($_POST['building'])) {
            // If they've tried and failed to log in
            echo '<p class="text-danger">Could not create room</p>';
        }
        ?>
        <form action="add-room.php" method="post">
            <legend>Add a room</legend>
            <label for="building" class="form-label">Building:</label>
            <input list="building-options" name="building" id="building" size="30" class="form-control"/>
            <label for="room-number" class="form-label">Room Number:</label>
            <input type="number" name="room-number" id="room-number" size="30" class="form-control">
            <button type="submit" name="add-room" class="btn btn-dark mt-3">Add Room</button>
        </form>
        </div>
    </div>
</div>
<?php
// Creates a datalist for the building input
if ($result = $stmt->get_result()) {
    // Start datalist
    print '<datalist id="building-options">';
    
    // Build options
    while ($row = mysqli_fetch_array($result)) {
        print '<option value="'. $row["building"] . '">';
    }

    // Close datalist
    print '</table>';

} 
print_footer();
?>
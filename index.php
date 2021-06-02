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
include_once('includes/mysqli_connect.php');


print_header();
?>

<div class="container">
    <div class="row">
        <div class="col-4">
        <?php 
        // This variable is passed by reference to hold the first 
        // room in the sidebar which is the default room for the 
        // booking form/bookings
        $first_room_arr = [];
        build_index_sidebar($dbc, $first_room_arr);
        ?>
        </div>
        <div id="room-container" class="col-8">
            <?php print_room_selection_form($first_room_arr) ?>
            
            <div class="row">
                <div class="col" id="bookings-container"></div>
            </div>
        </div>
    </div>
</div>

<?php print_footer(); ?>
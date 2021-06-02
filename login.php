<?php 
// Include functions
include_once('includes/functions.php');

// Start session
session_start();

// Redirect to index.php if logged in
if (logged_in()) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    // If the user has just tried to log in
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Include the database connection:
    include_once('includes/mysqli_connect.php');

    // Query to check for email/password combo
    $query = "SELECT * FROM users
                WHERE email = '" . $email . "'
                AND password_digest = md5('" . $password . "')";

    if ($result = mysqli_query($dbc, $query)) {
        // Check for non-empty set
        if ($row = mysqli_fetch_array($result)) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            
            mysqli_close($dbc);

            header('Location: index.php');
            exit;
        }
    } else { // Query didn't run
        print '<p style="color:red;">Could not retrieve the
        data becaues:<br>' . mysqli_error($dbc) . '.</p><p>
        The query being run was: ' . $query . '</p>';
    } 
}

print_header();
 
?>
<div class="container">
    <div class="row">
        <div class="col-3">
        <?php 
        if (isset($email)) {
            // If they've tried and failed to log in
            echo '<p class="text-danger">Invalid email or password.</p>';
        }
        ?>
        <form action="login.php" method="post">
            <legend>Login Now!</legend>
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" size="30" class="form-control"/>
            <label for="pasword" class="form-label">Password:</label>
            <input type="password" name="password" id="password" size="30" class="form-control">
            <button type="submit" name="login" class="btn btn-dark mt-3">Login</button>
        </form>
        </div>
    </div>
</div>

   
<?php print_footer(); ?>



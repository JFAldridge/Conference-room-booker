<?php 
/* This script connects to the database and establishes the character set for the communications */

// Connect:
$dbc = mysqli_connect('localhost', 'josald6_ei', 'lz3M0p8GkZ', 'josald6_ei');

// Set the character set
mysqli_set_charset($dbc, 'utf8');
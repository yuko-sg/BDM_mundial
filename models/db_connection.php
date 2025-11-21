<?php
// Database configuration
define('host', 'localhost:3306');
define('user', 'root');
define('password', '');
define('db_name', 'bdm_mundial');

// Create connection
function getConnection() {
    $conn = new mysqli(host, user, password, db_name);
    
    // Check connection
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    return $conn;
}
?>


<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'church_donations');

// Paystack API keys
define('PAYSTACK_SECRET_KEY', 'sk_test_28c1c0aa19c308c094962153bc53e4a1d208b7ce');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_7da18c9a8ae1672518fd710ac639ca89644f013d');

// Site URL
define('SITE_URL', 'https://your-website.com');

// Connect to database
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Error and exception handling
function error_handler($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . ": [$errno] $errstr in $errfile on line $errline";
    error_log($error_message . PHP_EOL, 3, 'error.log');
    
    if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "An error occurred. Please try again later.";
        exit(1);
    }
    
    return true;
}

function exception_handler($exception) {
    $error_message = date('Y-m-d H:i:s') . ": Uncaught Exception: " . $exception->getMessage() . 
                     " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($error_message . PHP_EOL, 3, 'error.log');
    
    echo "An error occurred. Please try again later.";
    exit(1);
}

// Set error and exception handlers
set_error_handler('error_handler');
set_exception_handler('exception_handler');
?>


<?php
// Load configuration
require_once 'config.php';

// Create connection without database selection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

// Create donations table
$sql = "CREATE TABLE IF NOT EXISTS donations (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    donation_type ENUM('one-time', 'recurring') NOT NULL,
    purpose VARCHAR(100) NOT NULL DEFAULT 'General Fund',
    frequency ENUM('one-time', 'weekly', 'biweekly', 'monthly', 'quarterly', 'annually') NOT NULL DEFAULT 'one-time',
    cover_fees TINYINT(1) NOT NULL DEFAULT 0,
    reference VARCHAR(100) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    payment_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Donations table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create recurring_donations table for managing recurring payments
$sql = "CREATE TABLE IF NOT EXISTS recurring_donations (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donation_id INT(11) UNSIGNED NOT NULL,
    next_payment_date DATE NOT NULL,
    status ENUM('active', 'paused', 'cancelled') NOT NULL DEFAULT 'active',
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Recurring donations table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create donation_logs table for tracking payment history
$sql = "CREATE TABLE IF NOT EXISTS donation_logs (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donation_id INT(11) UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    status ENUM('success', 'failed') NOT NULL,
    payment_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Donation logs table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create events table
$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Events table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create sermons table
$sql = "CREATE TABLE IF NOT EXISTS sermons (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    preacher VARCHAR(255) NOT NULL,
    sermon_date DATE NOT NULL,
    image VARCHAR(255),
    pdf_file VARCHAR(255),
    audio_file VARCHAR(255),
    category VARCHAR(100) NOT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Sermons table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Create gallery table
$sql = "CREATE TABLE IF NOT EXISTS gallery (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Gallery table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

echo "Database setup completed successfully!";

$conn->close();
?>


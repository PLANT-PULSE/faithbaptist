<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No event ID provided";
    header('Location: events.php');
    exit;
}

$id = (int)$_GET['id'];
$conn = db_connect();

// Get event data
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Event not found";
    header('Location: events.php');
    exit;
}

$event = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $location = $_POST['location'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Validate form data
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (empty($event_date)) {
        $errors[] = "Event date is required";
    }
    
    if (empty($event_time)) {
        $errors[] = "Event time is required";
    }
    
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    
    // Handle image upload
    $image_filename = $event['image']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/events/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Get file info
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed";
        }
        
        // Check file size (max 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            $errors[] = "File size must be less than 5MB";
        }
        
        // Generate unique filename
        $new_image_filename = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_image_filename;
        
        // Move uploaded file
        if (empty($errors) && move_uploaded_file($file_tmp, $upload_path)) {
            // Delete old image if it exists
            if (!empty($image_filename) && file_exists($upload_dir . $image_filename)) {
                unlink($upload_dir . $image_filename);
            }
            
            $image_filename = $new_image_filename;
        } else {
            $errors[] = "Failed to upload image";
        }
    }
    
    // If no errors, update event in database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, event_time = ?, location = ?, image = ?, is_featured = ? WHERE id = ?");
        $stmt->bind_param("ssssssii", $title, $description, $event_date, $event_time, $location, $image_filename, $is_featured, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Event updated successfully";
            header('Location: events.php');
            exit;
        } else {
            $errors[] = "Error updating event: " . $conn->error;
        }
        
        $stmt->close();
    }
    
    // If there were errors, update the event data with the submitted values
    if (!empty($errors)) {
        $event['title'] = $title;
        $event['description'] = $description;
        $event['event_date'] = $event_date;
        $event['event_time'] = $event_time;
        $event['location'] = $location;
        $event['is_featured'] = $is_featured;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Faith Baptist Church</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .header {
            background-color: #5d3b8c;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
        }
        
        .logo span {
            color: #f8b500;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            transition: background-color 0.3s;
        }
        
        .user-info a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar {
            width: 250px;
            background-color: white;
            height: calc(100vh - 60px);
            position: fixed;
            top: 60px;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #212529;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #f8f9fa;
            color: #5d3b8c;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .page-title {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 700;
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #5d3b8c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #4a2e70;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: inherit;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        
        .form-group input[type="file"] {
            padding: 8px 0;
        }
        
        .form-group input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .form-check {
            display: flex;
            align-items: center;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .current-image {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        .current-image img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                Faith Baptist <span>Church</span> Admin
            </div>
            <div class="user-info">
                <span>Welcome, Admin</span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>
    
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="donations.php"><i class="fas fa-hand-holding-usd"></i> Donations</a></li>
            <li><a href="recurring.php"><i class="fas fa-sync-alt"></i> Recurring Donations</a></li>
            <li><a href="events.php" class="active"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <h1 class="page-title">Edit Event</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Event Details</h2>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Event Title</label>
                        <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($event['title']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" required value="<?php echo $event['event_date']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="event_time">Event Time</label>
                        <input type="time" id="event_time" name="event_time" required value="<?php echo $event['event_time']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($event['location']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Event Image</label>
                        <?php if (!empty($event['image']) && file_exists("../uploads/events/{$event['image']}")): ?>
                            <div class="current-image">
                                <p>Current image:</p>
                                <img src="../uploads/events/<?php echo $event['image']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small style="display: block; margin-top: 5px; color: #6c757d;">Recommended size: 800x600 pixels. Max file size: 5MB. Leave empty to keep current image.</small>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="is_featured" name="is_featured" <?php echo $event['is_featured'] ? 'checked' : ''; ?>>
                        <label for="is_featured">Feature this event on the homepage</label>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn">Update Event</button>
                        <a href="events.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>


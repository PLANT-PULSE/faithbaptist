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

// Get categories for dropdown
$conn = db_connect();
$categories_query = "SELECT DISTINCT category FROM sermons ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $preacher = $_POST['preacher'] ?? '';
    $sermon_date = $_POST['sermon_date'] ?? '';
    $category = $_POST['category'] ?? '';
    $new_category = $_POST['new_category'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Use new category if provided
    if (!empty($new_category)) {
        $category = $new_category;
    }
    
    // Validate form data
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (empty($preacher)) {
        $errors[] = "Preacher name is required";
    }
    
    if (empty($sermon_date)) {
        $errors[] = "Sermon date is required";
    }
    
    if (empty($category)) {
        $errors[] = "Category is required";
    }
    
    // Handle file uploads
    $image_filename = '';
    $pdf_filename = '';
    $audio_filename = '';
    
    // Create upload directories if they don't exist
    $upload_dirs = [
        '../uploads/sermons/images/',
        '../uploads/sermons/pdfs/',
        '../uploads/sermons/audio/'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check file extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif','webp'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, PNG, WEBP and GIF files are allowed for images";
        }
        
        // Check file size (max 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            $errors[] = "Image file size must be less than 5MB";
        }
        
        // Generate unique filename
        $image_filename = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dirs[0] . $image_filename;
        
        // Move uploaded file
        if (empty($errors) && !move_uploaded_file($file_tmp, $upload_path)) {
            $errors[] = "Failed to upload image";
        }
    }
    
    // Handle PDF upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['pdf_file']['tmp_name'];
        $file_name = $_FILES['pdf_file']['name'];
        $file_size = $_FILES['pdf_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check file extension
        if ($file_ext !== 'pdf') {
            $errors[] = "Only PDF files are allowed for sermon notes";
        }
        
        // Check file size (max 10MB)
        if ($file_size > 10 * 1024 * 1024) {
            $errors[] = "PDF file size must be less than 10MB";
        }
        
        // Generate unique filename
        $pdf_filename = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dirs[1] . $pdf_filename;
        
        // Move uploaded file
        if (empty($errors) && !move_uploaded_file($file_tmp, $upload_path)) {
            $errors[] = "Failed to upload PDF file";
        }
    }
    
    // Handle audio upload
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['audio_file']['tmp_name'];
        $file_name = $_FILES['audio_file']['name'];
        $file_size = $_FILES['audio_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check file extension
        $allowed_extensions = ['mp3', 'wav', 'm4a'];
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Only MP3, WAV, and M4A files are allowed for audio";
        }
        
        // Check file size (max 50MB)
        if ($file_size > 50 * 1024 * 1024) {
            $errors[] = "Audio file size must be less than 50MB";
        }
        
        // Generate unique filename
        $audio_filename = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dirs[2] . $audio_filename;
        
        // Move uploaded file
        if (empty($errors) && !move_uploaded_file($file_tmp, $upload_path)) {
            $errors[] = "Failed to upload audio file";
        }
    }
    
    // If no errors, insert sermon into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO sermons (title, description, preacher, sermon_date, image, pdf_file, audio_file, category, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssi", $title, $description, $preacher, $sermon_date, $image_filename, $pdf_filename, $audio_filename, $category, $is_featured);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Sermon added successfully";
            header('Location: sermons.php');
            exit;
        } else {
            $errors[] = "Error adding sermon: " . $conn->error;
            
            // Delete uploaded files if there was an error
            if (!empty($image_filename) && file_exists($upload_dirs[0] . $image_filename)) {
                unlink($upload_dirs[0] . $image_filename);
            }
            if (!empty($pdf_filename) && file_exists($upload_dirs[1] . $pdf_filename)) {
                unlink($upload_dirs[1] . $pdf_filename);
            }
            if (!empty($audio_filename) && file_exists($upload_dirs[2] . $audio_filename)) {
                unlink($upload_dirs[2] . $audio_filename);
            }
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sermon - Faith Baptist Church</title>
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
        
        .category-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
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
            <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="sermons.php" class="active"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <h1 class="page-title">Add New Sermon</h1>
        
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
                <h2 class="card-title">Sermon Details</h2>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Sermon Title</label>
                        <input type="text" id="title" name="title" required value="<?php echo $_POST['title'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo $_POST['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="preacher">Preacher</label>
                        <input type="text" id="preacher" name="preacher" required value="<?php echo $_POST['preacher'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sermon_date">Sermon Date</label>
                        <input type="date" id="sermon_date" name="sermon_date" required value="<?php echo $_POST['sermon_date'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (isset($_POST['category']) && $_POST['category'] === $cat) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div class="category-section">
                            <label for="new_category">Or Create New Category</label>
                            <input type="text" id="new_category" name="new_category" value="<?php echo $_POST['new_category'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Sermon Image (Optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small style="display: block; margin-top: 5px; color: #6c757d;">Recommended size: 800x600 pixels. Max file size: 5MB.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="pdf_file">Sermon Notes PDF (Optional)</label>
                        <input type="file" id="pdf_file" name="pdf_file" accept=".pdf">
                        <small style="display: block; margin-top: 5px; color: #6c757d;">Max file size: 10MB.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="audio_file">Sermon Audio (Optional)</label>
                        <input type="file" id="audio_file" name="audio_file" accept=".mp3,.wav,.m4a">
                        <small style="display: block; margin-top: 5px; color: #6c757d;">Accepted formats: MP3, WAV, M4A. Max file size: 50MB.</small>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="is_featured" name="is_featured" <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                        <label for="is_featured">Feature this sermon on the homepage</label>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn">Add Sermon</button>
                        <a href="sermons.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>


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

// Get gallery items from database
$conn = db_connect();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_category = isset($_GET['filter_category']) ? $_GET['filter_category'] : 'all';

// Build query
$query = "SELECT * FROM gallery WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM gallery WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
    $count_query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}

// Add filter condition
if ($filter_category !== 'all') {
    $filter_category = $conn->real_escape_string($filter_category);
    $query .= " AND category = '$filter_category'";
    $count_query .= " AND category = '$filter_category'";
}

// Get total records
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get categories for filter dropdown
$categories_query = "SELECT DISTINCT category FROM gallery ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Add pagination
$query .= " ORDER BY created_at DESC LIMIT $offset, $limit";

// Execute query
$result = $conn->query($query);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get image filename before deleting
    $image_query = "SELECT image FROM gallery WHERE id = $id";
    $image_result = $conn->query($image_query);
    
    if ($image_result->num_rows > 0) {
        $image_row = $image_result->fetch_assoc();
        $image_file = $image_row['image'];
        
        // Delete the image file if it exists
        if (!empty($image_file) && file_exists("../uploads/gallery/$image_file")) {
            unlink("../uploads/gallery/$image_file");
        }
    }
    
    // Delete the gallery item
    $delete_query = "DELETE FROM gallery WHERE id = $id";
    if ($conn->query($delete_query) === TRUE) {
        $_SESSION['success_message'] = "Gallery item deleted successfully";
    } else {
        $_SESSION['error_message'] = "Error deleting gallery item: " . $conn->error;
    }
    
    // Redirect to refresh the page
    header('Location: gallery.php');
    exit;
}

// Handle feature/unfeature action
if (isset($_GET['action']) && ($_GET['action'] === 'feature' || $_GET['action'] === 'unfeature') && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $is_featured = $_GET['action'] === 'feature' ? 1 : 0;
    
    $update_query = "UPDATE gallery SET is_featured = $is_featured WHERE id = $id";
    if ($conn->query($update_query) === TRUE) {
        $_SESSION['success_message'] = "Gallery item " . ($is_featured ? "featured" : "unfeatured") . " successfully";
    } else {
        $_SESSION['error_message'] = "Error updating gallery item: " . $conn->error;
    }
    
    // Redirect to refresh the page
    header('Location: gallery.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Faith Baptist Church</title>
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
            overflow-y: auto;
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
        
        .search-filter {
            margin-bottom: 20px;
        }
        
        .search-filter form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .search-filter input, .search-filter select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: inherit;
            width: 100%;
        }
        
        .search-filter button {
            padding: 8px 15px;
            background-color: #5d3b8c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .search-filter button:hover {
            background-color: #4a2e70;
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
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .gallery-item {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .gallery-info {
            padding: 15px;
        }
        
        .gallery-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .gallery-category {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .gallery-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #f8b500;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            border-radius: 5px;
            background-color: white;
            color: #5d3b8c;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .pagination a:hover, .pagination a.active {
            background-color: #5d3b8c;
            color: white;
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
            <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="gallery.php" class="active"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <h1 class="page-title">Gallery Management</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Gallery Items</h2>
                <a href="add-gallery.php" class="btn"><i class="fas fa-plus"></i> Add New Image</a>
            </div>
            <div class="card-body">
                <div class="search-filter">
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Search by title or description" value="<?php echo htmlspecialchars($search); ?>">
                        
                        <select name="filter_category">
                            <option value="all" <?php echo $filter_category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $filter_category === $category ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="submit">Filter</button>
                        <a href="gallery.php" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
                
                <div class="gallery-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="gallery-item">
                                <?php if ($row['is_featured']): ?>
                                    <div class="featured-badge">Featured</div>
                                <?php endif; ?>
                                
                                <?php if (!empty($row['image']) && file_exists("../uploads/gallery/{$row['image']}")): ?>
                                    <img src="../uploads/gallery/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="gallery-image">
                                <?php else: ?>
                                    <img src="../uploads/placeholder.jpg" alt="Placeholder" class="gallery-image">
                                <?php endif; ?>
                                
                                <div class="gallery-info">
                                    <h3 class="gallery-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <div class="gallery-category"><?php echo htmlspecialchars($row['category']); ?></div>
                                    
                                    <div class="gallery-actions">
                                        <a href="edit-gallery.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                        
                                        <?php if ($row['is_featured']): ?>
                                            <a href="gallery.php?action=unfeature&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary" title="Unfeature"><i class="fas fa-star"></i> Unfeature</a>
                                        <?php else: ?>
                                            <a href="gallery.php?action=feature&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary" title="Feature"><i class="far fa-star"></i> Feature</a>
                                        <?php endif; ?>
                                        
                                        <a href="gallery.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this gallery item?');"><i class="fas fa-trash"></i> Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 30px;">
                            <p>No gallery items found</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter_category=<?php echo urlencode($filter_category); ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter_category=<?php echo urlencode($filter_category); ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter_category=<?php echo urlencode($filter_category); ?>"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>


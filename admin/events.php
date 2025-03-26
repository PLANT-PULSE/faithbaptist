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

// Get events from database
$conn = db_connect();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT * FROM events WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM events WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%' OR location LIKE '%$search%')";
    $count_query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%' OR location LIKE '%$search%')";
}

// Get total records
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Add pagination
$query .= " ORDER BY event_date DESC LIMIT $offset, $limit";

// Execute query
$result = $conn->query($query);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get image filename before deleting
    $image_query = "SELECT image FROM events WHERE id = $id";
    $image_result = $conn->query($image_query);
    
    if ($image_result->num_rows > 0) {
        $image_row = $image_result->fetch_assoc();
        $image_file = $image_row['image'];
        
        // Delete the image file if it exists
        if (!empty($image_file) && file_exists("../uploads/events/$image_file")) {
            unlink("../uploads/events/$image_file");
        }
    }
    
    // Delete the event
    $delete_query = "DELETE FROM events WHERE id = $id";
    if ($conn->query($delete_query) === TRUE) {
        $_SESSION['success_message'] = "Event deleted successfully";
    } else {
        $_SESSION['error_message'] = "Error deleting event: " . $conn->error;
    }
    
    // Redirect to refresh the page
    header('Location: events.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - Faith Baptist Church</title>
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
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .search-filter input, .search-filter select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-family: inherit;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        table tr:hover {
            background-color: #f8f9fa;
        }
        
        .featured {
            padding: 3px 8px;
            background-color: #d4edda;
            color: #28a745;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .actions a {
            color: #5d3b8c;
            text-decoration: none;
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
        
        .event-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
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
        <h1 class="page-title">Events Management</h1>
        
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
                <h2 class="card-title">Events List</h2>
                <a href="add-event.php" class="btn"><i class="fas fa-plus"></i> Add New Event</a>
            </div>
            <div class="card-body">
                <div class="search-filter">
                    <form action="" method="get" style="display: flex; gap: 15px; width: 100%;">
                        <input type="text" name="search" placeholder="Search by title, description or location" style="flex: 1;" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row['image']) && file_exists("../uploads/events/{$row['image']}")): ?>
                                            <img src="../uploads/events/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="event-image">
                                        <?php else: ?>
                                            <img src="../uploads/placeholder.jpg" alt="Placeholder" class="event-image">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($row['event_date'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($row['event_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo $row['is_featured'] ? '<span class="featured">Featured</span>' : ''; ?></td>
                                    <td class="actions">
                                        <a href="edit-event.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="events.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No events found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>


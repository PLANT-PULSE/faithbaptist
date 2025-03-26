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

// Get donations from database
$conn = db_connect();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query
$query = "SELECT * FROM donations WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM donations WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reference LIKE '%$search%')";
    $count_query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reference LIKE '%$search%')";
}

// Add filter condition
if ($filter !== 'all') {
    $filter = $conn->real_escape_string($filter);
    $query .= " AND donation_type = '$filter'";
    $count_query .= " AND donation_type = '$filter'";
}

// Get total records
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Add pagination
$query .= " ORDER BY payment_date DESC LIMIT $offset, $limit";

// Execute query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Faith Baptist Church</title>
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
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .change {
            font-size: 14px;
            color: #28a745;
        }
        
        .stat-card .change.negative {
            color: #dc3545;
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
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status.completed {
            background-color: #d4edda;
            color: #28a745;
        }
        
        .status.pending {
            background-color: #fff3cd;
            color: #ffc107;
        }
        
        .status.failed {
            background-color: #f8d7da;
            color: #dc3545;
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
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="donations.php"><i class="fas fa-hand-holding-usd"></i> Donations</a></li>
            <li><a href="recurring.php"><i class="fas fa-sync-alt"></i> Recurring Donations</a></li>
            <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <h1 class="page-title">Dashboard</h1>
        
        <div class="dashboard-stats">
            <?php
            // Get total donations
            $total_query = "SELECT SUM(amount) as total FROM donations WHERE status = 'completed'";
            $total_result = $conn->query($total_query);
            $total_row = $total_result->fetch_assoc();
            $total_donations = $total_row['total'] ?? 0;
            
            // Get today's donations
            $today_query = "SELECT SUM(amount) as total FROM donations WHERE status = 'completed' AND DATE(payment_date) = CURDATE()";
            $today_result = $conn->query($today_query);
            $today_row = $today_result->fetch_assoc();
            $today_donations = $today_row['total'] ?? 0;
            
            // Get this month's donations
            $month_query = "SELECT SUM(amount) as total FROM donations WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())";
            $month_result = $conn->query($month_query);
            $month_row = $month_result->fetch_assoc();
            $month_donations = $month_row['total'] ?? 0;
            
            // Get recurring donations count
            $recurring_query = "SELECT COUNT(*) as total FROM donations WHERE donation_type = 'recurring' AND status = 'completed'";
            $recurring_result = $conn->query($recurring_query);
            $recurring_row = $recurring_result->fetch_assoc();
            $recurring_count = $recurring_row['total'] ?? 0;
            ?>
            
            <div class="stat-card">
                <h3>Total Donations</h3>
                <div class="value">$<?php echo number_format($total_donations, 2); ?></div>
                <div class="change">All time</div>
            </div>
            
            <div class="stat-card">
                <h3>Today's Donations</h3>
                <div class="value">$<?php echo number_format($today_donations, 2); ?></div>
                <div class="change"><?php echo date('F j, Y'); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>This Month</h3>
                <div class="value">$<?php echo number_format($month_donations, 2); ?></div>
                <div class="change"><?php echo date('F Y'); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Recurring Donations</h3>
                <div class="value"><?php echo $recurring_count; ?></div>
                <div class="change">Active subscriptions</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Donations</h2>
                <a href="donations.php" style="color: #5d3b8c; text-decoration: none;">View All</a>
            </div>
            <div class="card-body">
                <div class="search-filter">
                    <form action="" method="get" style="display: flex; gap: 15px; width: 100%;">
                        <input type="text" name="search" placeholder="Search by name, email or reference" style="flex: 1;" value="<?php echo htmlspecialchars($search); ?>">
                        <select name="filter">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Types</option>
                            <option value="one-time" <?php echo $filter === 'one-time' ? 'selected' : ''; ?>>One-time</option>
                            <option value="recurring" <?php echo $filter === 'recurring' ? 'selected' : ''; ?>>Recurring</option>
                        </select>
                        <button type="submit">Search</button>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo ucfirst($row['donation_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($row['payment_date'])); ?></td>
                                    <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    <td class="actions">
                                        <a href="view-donation.php?id=<?php echo $row['id']; ?>" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="export-receipt.php?id=<?php echo $row['id']; ?>" title="Export Receipt"><i class="fas fa-file-pdf"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No donations found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page <  $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>


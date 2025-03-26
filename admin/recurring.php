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

// Get recurring donations from database
$conn = db_connect();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all';
$filter_frequency = isset($_GET['filter_frequency']) ? $_GET['filter_frequency'] : 'all';

// Build query
$query = "SELECT d.*, rd.id as recurring_id, rd.next_payment_date, rd.status as recurring_status 
          FROM donations d 
          JOIN recurring_donations rd ON d.id = rd.donation_id 
          WHERE d.donation_type = 'recurring'";
$count_query = "SELECT COUNT(*) as total 
                FROM donations d 
                JOIN recurring_donations rd ON d.id = rd.donation_id 
                WHERE d.donation_type = 'recurring'";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (d.name LIKE '%$search%' OR d.email LIKE '%$search%' OR d.reference LIKE '%$search%')";
    $count_query .= " AND (d.name LIKE '%$search%' OR d.email LIKE '%$search%' OR d.reference LIKE '%$search%')";
}

// Add filter conditions
if ($filter_status !== 'all') {
    $filter_status = $conn->real_escape_string($filter_status);
    $query .= " AND rd.status = '$filter_status'";
    $count_query .= " AND rd.status = '$filter_status'";
}

if ($filter_frequency !== 'all') {
    $filter_frequency = $conn->real_escape_string($filter_frequency);
    $query .= " AND d.frequency = '$filter_frequency'";
    $count_query .= " AND d.frequency = '$filter_frequency'";
}

// Get total records
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Add pagination
$query .= " ORDER BY rd.next_payment_date ASC LIMIT $offset, $limit";

// Execute query
$result = $conn->query($query);

// Calculate totals
$total_query = "SELECT 
    COUNT(*) as total_recurring,
    COUNT(CASE WHEN rd.status = 'active' THEN 1 END) as active_recurring,
    COUNT(CASE WHEN rd.status = 'paused' THEN 1 END) as paused_recurring,
    COUNT(CASE WHEN rd.status = 'cancelled' THEN 1 END) as cancelled_recurring,
    SUM(d.amount) as total_amount,
    SUM(CASE WHEN rd.status = 'active' THEN d.amount ELSE 0 END) as active_amount
    FROM donations d 
    JOIN recurring_donations rd ON d.id = rd.donation_id 
    WHERE d.donation_type = 'recurring'";

// Apply the same filters to the totals
if (!empty($search)) {
    $total_query .= " AND (d.name LIKE '%$search%' OR d.email LIKE '%$search%' OR d.reference LIKE '%$search%')";
}

if ($filter_status !== 'all') {
    $total_query .= " AND rd.status = '$filter_status'";
}

if ($filter_frequency !== 'all') {
    $total_query .= " AND d.frequency = '$filter_frequency'";
}

$total_result = $conn->query($total_query);
$totals = $total_result->fetch_assoc();

// Handle status update
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    if (in_array($status, ['active', 'paused', 'cancelled'])) {
        $update_query = "UPDATE recurring_donations SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Recurring donation status updated successfully";
        } else {
            $_SESSION['error_message'] = "Error updating status: " . $conn->error;
        }
        
        $stmt->close();
        
        // Redirect to refresh the page
        header('Location: recurring.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurring Donations - Faith Baptist Church</title>
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
        
        .btn-warning {
            background-color: #ffc107;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
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
        
        .status.active {
            background-color: #d4edda;
            color: #28a745;
        }
        
        .status.paused {
            background-color: #fff3cd;
            color: #ffc107;
        }
        
        .status.cancelled {
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
        
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .dropdown-content a {
            color: #212529;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }
        
        .dropdown-content a:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
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
            <li><a href="recurring.php" class="active"><i class="fas fa-sync-alt"></i> Recurring Donations</a></li>
            <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <h1 class="page-title">Recurring Donations</h1>
        
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
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Recurring</h3>
                <div class="value"><?php echo $totals['total_recurring'] ?? 0; ?></div>
                <div class="change">Subscriptions</div>
            </div>
            
            <div class="stat-card">
                <h3>Active Subscriptions</h3>
                <div class="value"><?php echo $totals['active_recurring'] ?? 0; ?></div>
                <div class="change">$<?php echo number_format($totals['active_amount'] ?? 0, 2); ?> monthly</div>
            </div>
            
            <div class="stat-card">
                <h3>Paused</h3>
                <div class="value"><?php echo $totals['paused_recurring'] ?? 0; ?></div>
                <div class="change">Temporarily paused</div>
            </div>
            
            <div class="stat-card">
                <h3>Cancelled</h3>
                <div class="value"><?php echo $totals['cancelled_recurring'] ?? 0; ?></div>
                <div class="change">No longer active</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recurring Donations</h2>
            </div>
            <div class="card-body">
                <div class="search-filter">
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Search by name, email or reference" value="<?php echo htmlspecialchars($search); ?>">
                        
                        <select name="filter_status">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="paused" <?php echo $filter_status === 'paused' ? 'selected' : ''; ?>>Paused</option>
                            <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        
                        <select name="filter_frequency">
                            <option value="all" <?php echo $filter_frequency === 'all' ? 'selected' : ''; ?>>All Frequencies</option>
                            <option value="weekly" <?php echo $filter_frequency === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                            <option value="biweekly" <?php echo $filter_frequency === 'biweekly' ? 'selected' : ''; ?>>Bi-weekly</option>
                            <option value="monthly" <?php echo $filter_frequency === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                            <option value="quarterly" <?php echo $filter_frequency === 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                            <option value="annually" <?php echo $filter_frequency === 'annually' ? 'selected' : ''; ?>>Annually</option>
                        </select>
                        
                        <button type="submit">Filter</button>
                        <a href="recurring.php" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Frequency</th>
                            <th>Next Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div style="font-size: 12px; color: #6c757d;"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo ucfirst($row['frequency']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($row['next_payment_date'])); ?></td>
                                    <td><span class="status <?php echo $row['recurring_status']; ?>"><?php echo ucfirst($row['recurring_status']); ?></span></td>
                                    <td class="actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm">Actions <i class="fas fa-caret-down"></i></button>
                                            <div class="dropdown-content">
                                                <a href="view-donation.php?id=<?php echo $row['id']; ?>">View Details</a>
                                                <?php if ($row['recurring_status'] === 'active'): ?>
                                                    <a href="recurring.php?action=update_status&id=<?php echo $row['recurring_id']; ?>&status=paused" onclick="return confirm('Are you sure you want to pause this recurring donation?');">Pause Subscription</a>
                                                    <a href="recurring.php?action=update_status&id=<?php echo $row['recurring_id']; ?>&status=cancelled" onclick="return confirm('Are you sure you want to cancel this recurring donation?');">Cancel Subscription</a>
                                                <?php elseif ($row['recurring_status'] === 'paused'): ?>
                                                    <a href="recurring.php?action=update_status&id=<?php echo $row['recurring_id']; ?>&status=active" onclick="return confirm('Are you sure you want to reactivate this recurring donation?');">Reactivate Subscription</a>
                                                    <a href="recurring.php?action=update_status&id=<?php echo $row['recurring_id']; ?>&status=cancelled" onclick="return confirm('Are you sure you want to cancel this recurring donation?');">Cancel Subscription</a>
                                                <?php elseif ($row['recurring_status'] === 'cancelled'): ?>
                                                    <a href="recurring.php?action=update_status&id=<?php echo $row['recurring_id']; ?>&status=active" onclick="return confirm('Are you sure you want to reactivate this recurring donation?');">Reactivate Subscription</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No recurring donations found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter_status=<?php echo urlencode($filter_status); ?>&filter_frequency=<?php echo urlencode($filter_frequency); ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter_status=<?php echo urlencode($filter_status); ?>&filter_frequency=<?php echo urlencode($filter_frequency); ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter_status=<?php echo urlencode($filter_status); ?>&filter_frequency=<?php echo urlencode($filter_frequency); ?>"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>


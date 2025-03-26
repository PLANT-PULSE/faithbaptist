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
$limit = 15;
$offset = ($page - 1) * $limit;

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 'all';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all';
$filter_purpose = isset($_GET['filter_purpose']) ? $_GET['filter_purpose'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query
$query = "SELECT * FROM donations WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM donations WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reference LIKE '%$search%' OR transaction_id LIKE '%$search%')";
    $count_query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR reference LIKE '%$search%' OR transaction_id LIKE '%$search%')";
}

// Add filter conditions
if ($filter_type !== 'all') {
    $filter_type = $conn->real_escape_string($filter_type);
    $query .= " AND donation_type = '$filter_type'";
    $count_query .= " AND donation_type = '$filter_type'";
}

if ($filter_status !== 'all') {
    $filter_status = $conn->real_escape_string($filter_status);
    $query .= " AND status = '$filter_status'";
    $count_query .= " AND status = '$filter_status'";
}

if ($filter_purpose !== 'all') {
    $filter_purpose = $conn->real_escape_string($filter_purpose);
    $query .= " AND purpose = '$filter_purpose'";
    $count_query .= " AND purpose = '$filter_purpose'";
}

// Add date range filter
if (!empty($date_from)) {
    $date_from = $conn->real_escape_string($date_from);
    $query .= " AND DATE(payment_date) >= '$date_from'";
    $count_query .= " AND DATE(payment_date) >= '$date_from'";
}

if (!empty($date_to)) {
    $date_to = $conn->real_escape_string($date_to);
    $query .= " AND DATE(payment_date) <= '$date_to'";
    $count_query .= " AND DATE(payment_date) <= '$date_to'";
}

// Get total records
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $limit);

// Get donation purposes for filter dropdown
$purposes_query = "SELECT DISTINCT purpose FROM donations ORDER BY purpose";
$purposes_result = $conn->query($purposes_query);
$purposes = [];
while ($row = $purposes_result->fetch_assoc()) {
    $purposes[] = $row['purpose'];
}

// Add pagination
$query .= " ORDER BY payment_date DESC LIMIT $offset, $limit";

// Execute query
$result = $conn->query($query);

// Calculate totals
$total_query = "SELECT 
    SUM(amount) as total_amount,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount,
    COUNT(*) as total_donations,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_donations,
    COUNT(CASE WHEN donation_type = 'recurring' THEN 1 END) as recurring_donations
    FROM donations";

// Apply the same filters to the totals
if (!empty($search)) {
    $total_query .= " WHERE (name LIKE '%$search%' OR email LIKE '%$search%' OR reference LIKE '%$search%' OR transaction_id LIKE '%$search%')";
}

$total_result = $conn->query($total_query);
$totals = $total_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations Management - Faith Baptist Church</title>
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
        
        .export-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
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
            <li><a href="donations.php" class="active"><i class="fas fa-hand-holding-usd"></i> Donations</a></li>
            <li><a href="recurring.php"><i class="fas fa-sync-alt"></i> Recurring Donations</a></li>
            <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="sermons.php"><i class="fas fa-bible"></i> Sermons</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <h1 class="page-title">Donations Management</h1>
        
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
                <h3>Total Donations</h3>
                <div class="value">$<?php echo number_format($totals['total_amount'] ?? 0, 2); ?></div>
                <div class="change"><?php echo $totals['total_donations'] ?? 0; ?> donations</div>
            </div>
            
            <div class="stat-card">
                <h3>Completed Donations</h3>
                <div class="value">$<?php echo number_format($totals['completed_amount'] ?? 0, 2); ?></div>
                <div class="change"><?php echo $totals['completed_donations'] ?? 0; ?> donations</div>
            </div>
            
            <div class="stat-card">
                <h3>Recurring Donations</h3>
                <div class="value"><?php echo $totals['recurring_donations'] ?? 0; ?></div>
                <div class="change">Active subscriptions</div>
            </div>
            
            <div class="stat-card">
                <h3>Average Donation</h3>
                <div class="value">$<?php echo $totals['total_donations'] > 0 ? number_format($totals['total_amount'] / $totals['total_donations'], 2) : '0.00'; ?></div>
                <div class="change">Per donation</div>
            </div>
        </div>
        
        <div class="export-buttons">
            <a href="export-donations.php?format=csv<?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn btn-secondary">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="export-donations.php?format=pdf<?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn btn-secondary">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Donations List</h2>
            </div>
            <div class="card-body">
                <div class="search-filter">
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Search by name, email or reference" value="<?php echo htmlspecialchars($search); ?>">
                        
                        <select name="filter_type">
                            <option value="all" <?php echo $filter_type === 'all' ? 'selected' : ''; ?>>All Types</option>
                            <option value="one-time" <?php echo $filter_type === 'one-time' ? 'selected' : ''; ?>>One-time</option>
                            <option value="recurring" <?php echo $filter_type === 'recurring' ? 'selected' : ''; ?>>Recurring</option>
                        </select>
                        
                        <select name="filter_status">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="failed" <?php echo $filter_status === 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                        
                        <select name="filter_purpose">
                            <option value="all" <?php echo $filter_purpose === 'all' ? 'selected' : ''; ?>>All Purposes</option>
                            <?php foreach ($purposes as $purpose): ?>
                                <option value="<?php echo htmlspecialchars($purpose); ?>" <?php echo $filter_purpose === $purpose ? 'selected' : ''; ?>><?php echo htmlspecialchars($purpose); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div>
                            <label for="date_from">From:</label>
                            <input type="date" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                        </div>
                        
                        <div>
                            <label for="date_to">To:</label>
                            <input type="date" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                        </div>
                        
                        <button type="submit">Filter</button>
                        <a href="donations.php" class="btn btn-secondary">Reset</a>
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
                                        <a href="view-donation.php?id=<?php echo $row['id']; ?>" class="btn btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="export-receipt.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary" title="Export Receipt"><i class="fas fa-file-pdf"></i></a>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <a href="update-donation-status.php?id=<?php echo $row['id']; ?>&status=completed" class="btn btn-sm btn-success" title="Mark as Completed" onclick="return confirm('Are you sure you want to mark this donation as completed?');"><i class="fas fa-check"></i></a>
                                            <a href="update-donation-status.php?id=<?php echo $row['id']; ?>&status=failed" class="btn btn-sm btn-danger" title="Mark as Failed" onclick="return confirm('Are you sure you want to mark this donation as failed?');"><i class="fas fa-times"></i></a>
                                        <?php endif; ?>
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
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter_type=<?php echo urlencode($filter_type); ?>&filter_status=<?php echo urlencode($filter_status); ?>&filter_purpose=<?php echo urlencode($filter_purpose); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter_type=<?php echo urlencode($filter_type); ?>&filter_status=<?php echo urlencode($filter_status); ?>&filter_purpose=<?php echo urlencode($filter_purpose); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter_type=<?php echo urlencode($filter_type); ?>&filter_status=<?php echo urlencode($filter_status); ?>&filter_purpose=<?php echo urlencode($filter_purpose); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script>
        // Date range validation
        document.addEventListener('DOMContentLoaded', function() {
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            
            dateFromInput.addEventListener('change', function() {
                if (dateToInput.value && dateFromInput.value > dateToInput.value) {
                    dateToInput.value = dateFromInput.value;
                }
            });
            
            dateToInput.addEventListener('change', function() {
                if (dateFromInput.value && dateToInput.value < dateFromInput.value) {
                    dateFromInput.value = dateToInput.value;
                }
            });
        });
    </script>
</body>
</html>


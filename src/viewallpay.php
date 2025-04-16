<?php
require_once 'admin_check.php';
require_once 'databases.php';

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search and filter functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'all';

// Build the query
$query = "SELECT * FROM payments WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM payments WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR transaction_id LIKE '%$search%')";
    $count_query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR transaction_id LIKE '%$search%')";
}

if ($status_filter !== 'all') {
    $query .= " AND status = '$status_filter'";
    $count_query .= " AND status = '$status_filter'";
}

// Get total records for pagination
$total_result = $conn->query($count_query);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Final query with pagination
$query .= " ORDER BY payment_date DESC LIMIT $offset, $records_per_page";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Records - ClubSphere</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar-collapsed {
            width: 80px;
            overflow: hidden;
        }
        .sidebar-collapsed .nav-text {
            display: none;
        }
        .sidebar-collapsed .logo-text {
            display: none;
        }
        .sidebar-expanded {
            width: 260px;
        }
        .main-content {
            transition: margin-left 0.3s;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-expanded bg-gradient-to-b from-red-800 to-red-900 text-white flex flex-col">
            <div class="p-4 flex items-center justify-between border-b border-red-700">
                <div class="flex items-center">
                    
                    <span class="logo-text font-bold text-lg">ClubSphere</span>
                </div>
                <button id="toggle-sidebar" class="text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                <nav class="p-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="admin_dashboard.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition">
                                <i class="fas fa-gauge mr-3 w-5 text-center"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin_users.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition">
                                <i class="fas fa-users mr-3 w-5 text-center"></i>
                                <span class="nav-text">Manage Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="payment_records.php" class="flex items-center p-3 rounded-lg bg-white text-red-900 transition">
                                <i class="fas fa-credit-card mr-3 w-5 text-center"></i>
                                <span class="nav-text">Payment Records</span>
                            </a>
                        </li>
                        <li>
                            <a href="finance.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition">
                                <i class="fas fa-dollar-sign mr-3 w-5 text-center"></i>
                                <span class="nav-text">Finance</span>
                            </a>
                        </li>
                        <li>
                            <a href="event_planning.php" class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition">
                                <i class="fas fa-calendar-day mr-3 w-5 text-center"></i>
                                <span class="nav-text">Events</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <div class="p-4 border-t border-red-700">
                <a href="databases.php?logout=true" class="flex items-center p-3 rounded-lg bg-white text-red-900 hover:bg-gray-200 transition">
                    <i class="fas fa-right-from-bracket mr-3 w-5 text-center"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Payment Records</h1>
                        <div class="flex items-center space-x-4">
                            <a href="finance.php" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                <i class="fas fa-chart-line mr-2"></i>Finance Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Search and Filter Card -->
                    <div class="bg-white rounded-lg shadow mb-6 p-4">
                        <form method="get" action="payment_records.php" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <div class="relative">
                                    <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>"
                                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                           placeholder="Search by name, email or transaction ID">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="failed" <?= $status_filter === 'failed' ? 'selected' : '' ?>>Failed</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Payment Records Table -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($row['transaction_id']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= htmlspecialchars($row['name']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= htmlspecialchars($row['email']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                $<?= number_format($row['amount'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= ucfirst($row['payment_method']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?= $row['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($row['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= date('M j, Y g:i A', strtotime($row['payment_date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="view_payment.php?id=<?= $row['id'] ?>" 
                                                   class="text-blue-600 hover:text-blue-900 mr-3"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="invoice.php?id=<?= $row['id'] ?>" 
                                                   class="text-purple-600 hover:text-purple-900"
                                                   title="Download Invoice">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No payment records found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium"><?= $offset + 1 ?></span> to 
                                        <span class="font-medium"><?= min($offset + $records_per_page, $total_rows) ?></span> of 
                                        <span class="font-medium"><?= $total_rows ?></span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>"
                                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Previous</span>
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>"
                                               class="<?= $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                <?= $i ?>
                                            </a>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>"
                                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Next</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar
        document.getElementById('toggle-sidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
        });

        // Confirm before changing status
        document.querySelectorAll('.status-change').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to change this payment status?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
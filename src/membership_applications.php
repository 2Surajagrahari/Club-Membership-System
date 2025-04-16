<?php
include "admin_check.php";

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$where_clause = "";
if (in_array($status_filter, ['pending', 'approved', 'rejected'])) {
    $where_clause = "WHERE status = '$status_filter'";
}

// Fetch all applications
$applications = $conn->query("
    SELECT * FROM membership_applications 
    $where_clause
    ORDER BY application_date DESC
");

// Count applications by status
$count_all = $conn->query("SELECT COUNT(*) FROM membership_applications")->fetch_row()[0];
$count_pending = $conn->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'pending'")->fetch_row()[0];
$count_approved = $conn->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'approved'")->fetch_row()[0];
$count_rejected = $conn->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'rejected'")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Applications | ClubSphere</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-badge {
            @apply px-3 py-1 rounded-full text-xs font-medium;
        }
        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
        }
        .status-approved {
            @apply bg-green-100 text-green-800;
        }
        .status-rejected {
            @apply bg-red-100 text-red-800;
        }
        .filter-active {
            @apply bg-red-800 text-white;
        }
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Include your sidebar from admin_dashboard.php -->
        <?php include 'admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Membership Applications</h1>
                <div class="flex items-center space-x-4">
                    <a href="admin_dashboard.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Status Filters -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="flex flex-wrap gap-2">
                    <a href="membership_applications.php?status=all" 
                       class="px-4 py-2 rounded-full border hover:bg-gray-100 transition <?php echo $status_filter == 'all' ? 'filter-active' : ''; ?>">
                        All <span class="bg-gray-200 text-gray-800 rounded-full px-2 py-1 text-xs ml-1"><?php echo $count_all; ?></span>
                    </a>
                    <a href="membership_applications.php?status=pending" 
                       class="px-4 py-2 rounded-full border hover:bg-yellow-100 transition <?php echo $status_filter == 'pending' ? 'filter-active' : ''; ?>">
                        Pending <span class="bg-yellow-100 text-yellow-800 rounded-full px-2 py-1 text-xs ml-1"><?php echo $count_pending; ?></span>
                    </a>
                    <a href="membership_applications.php?status=approved" 
                       class="px-4 py-2 rounded-full border hover:bg-green-100 transition <?php echo $status_filter == 'approved' ? 'filter-active' : ''; ?>">
                        Approved <span class="bg-green-100 text-green-800 rounded-full px-2 py-1 text-xs ml-1"><?php echo $count_approved; ?></span>
                    </a>
                    <a href="membership_applications.php?status=rejected" 
                       class="px-4 py-2 rounded-full border hover:bg-red-100 transition <?php echo $status_filter == 'rejected' ? 'filter-active' : ''; ?>">
                        Rejected <span class="bg-red-100 text-red-800 rounded-full px-2 py-1 text-xs ml-1"><?php echo $count_rejected; ?></span>
                    </a>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-red-800 text-white">
                            <tr>
                                <th class="p-4 text-left">Name</th>
                                <th class="p-4 text-left">Email</th>
                                <th class="p-4 text-left">Date</th>
                                <th class="p-4 text-left">Status</th>
                                <th class="p-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($applications->num_rows > 0): ?>
                                <?php while ($app = $applications->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50 hover-scale">
                                    <td class="p-4 font-medium"><?php echo htmlspecialchars($app['name']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($app['email']); ?></td>
                                    <td class="p-4"><?php echo date('M j, Y', strtotime($app['application_date'])); ?></td>
                                    <td class="p-4">
                                        <span class="status-badge <?php echo 'status-' . $app['status']; ?>">
                                            <i class="fas fa-<?php echo $app['status'] == 'pending' ? 'clock' : ($app['status'] == 'approved' ? 'check-circle' : 'times-circle'); ?> mr-1"></i>
                                            <?php echo ucfirst($app['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex space-x-2">
                                            <a href="view_application.php?id=<?php echo $app['id']; ?>" 
                                               class="p-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($app['status'] == 'pending'): ?>
                                                <a href="process_application.php?action=approve&id=<?php echo $app['id']; ?>" 
                                                   class="p-2 bg-green-100 text-green-600 rounded hover:bg-green-200 transition"
                                                   title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="process_application.php?action=reject&id=<?php echo $app['id']; ?>" 
                                                   class="p-2 bg-red-100 text-red-600 rounded hover:bg-red-200 transition"
                                                   title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No applications found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bulk Actions -->
            <?php if ($applications->num_rows > 0): ?>
            <div class="mt-6 bg-white p-4 rounded-lg shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <select class="border rounded px-3 py-2 mr-2">
                            <option>Bulk Actions</option>
                            <option>Approve Selected</option>
                            <option>Reject Selected</option>
                        </select>
                        <button class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-700 transition">
                            Apply
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        Showing <?php echo $applications->num_rows; ?> of <?php echo $count_all; ?> applications
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Confirm before rejecting an application
        document.querySelectorAll('a[href*="action=reject"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to reject this application?')) {
                    e.preventDefault();
                }
            });
        });

        // Search functionality would go here
        // You can implement this with JavaScript or server-side filtering
    </script>
</body>
</html>
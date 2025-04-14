<?php
include "admin_check.php"; // Restrict to admins only

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle user role change
if (isset($_GET["promote"]) && is_numeric($_GET["promote"])) {
    $user_id = intval($_GET["promote"]);
    $conn->query("UPDATE users SET role = 'admin' WHERE id = $user_id");
    $_SESSION["success"] = "User promoted to admin successfully";
    header("Location: admin_users.php");
    exit();
}

if (isset($_GET["demote"]) && is_numeric($_GET["demote"])) {
    $user_id = intval($_GET["demote"]);
    $conn->query("UPDATE users SET role = 'user' WHERE id = $user_id");
    $_SESSION["success"] = "Admin demoted to regular user successfully";
    header("Location: admin_users.php");
    exit();
}

// Handle user approval
if (isset($_GET["approve"]) && is_numeric($_GET["approve"])) {
    $user_id = intval($_GET["approve"]);
    
    // Get user email first for notification
    $result = $conn->query("SELECT email, name FROM users WHERE id = $user_id");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_email = $user["email"];
        $user_name = $user["name"];
        
        // Update user status
        if ($conn->query("UPDATE users SET approved = 1 WHERE id = $user_id")) {
            // Send notification email (simplified, consider moving to a function)
            $subject = "ClubSphere Account Approved!";
            $message = "Hi $user_name,\n\nYour ClubSphere account has been approved. You can now log in.\n\nBest regards,\nClubSphere Team";
            $headers = "From: no-reply@clubsphere.com";
            mail($user_email, $subject, $message, $headers);
            
            $_SESSION["success"] = "User approved and notification email sent";
        }
    }
    
    header("Location: admin_users.php");
    exit();
}

// Handle user deletion
if (isset($_GET["delete"]) && is_numeric($_GET["delete"])) {
    $user_id = intval($_GET["delete"]);
    $conn->query("DELETE FROM users WHERE id = $user_id");
    $_SESSION["success"] = "User deleted successfully";
    header("Location: admin_users.php");
    exit();
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = "WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}

// Filtering
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
if ($filter === 'pending') {
    $search_condition = !empty($search_condition) ? $search_condition . " AND approved = 0" : "WHERE approved = 0";
} elseif ($filter === 'approved') {
    $search_condition = !empty($search_condition) ? $search_condition . " AND approved = 1" : "WHERE approved = 1";
} elseif ($filter === 'admins') {
    $search_condition = !empty($search_condition) ? $search_condition . " AND role = 'admin'" : "WHERE role = 'admin'";
}

// Count total users
$total_count = $conn->query("SELECT COUNT(*) FROM users $search_condition")->fetch_row()[0];
$total_pages = ceil($total_count / $limit);

// Get users for current page
$users = $conn->query("SELECT id, name, email, role, approved, 
                       DATE_FORMAT(created_at, '%M %d, %Y') as joined_date,
                       DATE_FORMAT(last_login, '%M %d, %Y') as last_login_date 
                       FROM users $search_condition 
                       ORDER BY id DESC LIMIT $offset, $limit");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manage Users | ClubSphere Admin</title>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-red-800 to-red-900 text-white min-h-screen p-6">
            <h2 class="text-2xl font-bold mb-6"><a href="index.php">ClubSphere</a></h2>
            <nav>
                <ul>
                    <li class="mb-4"><a href="admin_dashboard.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                    <li class="mb-4"><a href="admin_approve.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user-check"></i> User Approvals</a></li>
                    <li class="mb-4"><a href="admin_users.php" class="block p-2 bg-white text-red-900 rounded"><i class="fa-solid fa-users"></i> Manage Users</a></li>
                    <li class="mb-4"><a href="event_planning.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-calendar"></i> Manage Events</a></li>
                    <li class="mb-4"><a href="dashboard.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user"></i> User View</a></li>
                    <li class="mb-4"><a href="databases.php?logout=true" class="block p-2 bg-red-600 rounded hover:bg-red-700"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Top Header -->
            <div class="bg-gradient-to-r from-red-800 to-red-900 px-6 py-8 rounded-lg shadow-md mb-6">
                <h1 class="text-3xl font-bold text-white">Manage Users</h1>
                <p class="text-red-200">View, edit and manage all ClubSphere users</p>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION["success"])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?php echo $_SESSION["success"]; ?></p>
                </div>
                <?php unset($_SESSION["success"]); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION["error"])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?php echo $_SESSION["error"]; ?></p>
                </div>
                <?php unset($_SESSION["error"]); ?>
            <?php endif; ?>

            <!-- Search and Filter -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <form method="GET" action="" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>" 
                               class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="min-w-[150px]">
                        <select name="filter" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Users</option>
                            <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
                            <option value="approved" <?php echo $filter === 'approved' ? 'selected' : ''; ?>>Approved Users</option>
                            <option value="admins" <?php echo $filter === 'admins' ? 'selected' : ''; ?>>Administrators</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        <i class="fa-solid fa-search mr-2"></i> Search
                    </button>
                    
                    <?php if (!empty($search) || $filter !== 'all'): ?>
                        <a href="admin_users.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fa-solid fa-times mr-2"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Users Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <?php
                $total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
                $total_pending = $conn->query("SELECT COUNT(*) FROM users WHERE approved = 0")->fetch_row()[0];
                $total_admins = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetch_row()[0];
                $total_members = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND approved = 1")->fetch_row()[0];
                ?>
                
                <div class="bg-white p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $total_users; ?></p>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Pending Approval</h3>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $total_pending; ?></p>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Administrators</h3>
                    <p class="text-3xl font-bold text-red-600"><?php echo $total_admins; ?></p>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Active Members</h3>
                    <p class="text-3xl font-bold text-green-600"><?php echo $total_members; ?></p>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6 overflow-x-auto">
                <?php if ($users && $users->num_rows > 0): ?>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-red-800 text-white">
                                <th class="p-3 text-left">Name</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Role</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Joined Date</th>
                                <th class="p-3 text-left">Last Login</th>
                                <th class="p-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3"><?php echo htmlspecialchars($user["name"]); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($user["email"]); ?></td>
                                    <td class="p-3">
                                        <?php if ($user["role"] == "admin"): ?>
                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Admin</span>
                                        <?php else: ?>
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3">
                                        <?php if ($user["approved"] == 1): ?>
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Approved</span>
                                        <?php else: ?>
                                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3"><?php echo $user["joined_date"] ?? 'N/A'; ?></td>
                                    <td class="p-3"><?php echo $user["last_login_date"] ?? 'Never'; ?></td>
                                    <td class="p-3">
                                        <div class="flex flex-wrap gap-2">
                                            <?php if ($user["role"] == "user"): ?>
                                                <a href="?promote=<?php echo $user["id"]; ?>" onclick="return confirm('Are you sure you want to promote this user to admin?')" class="px-2 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-sm">
                                                    <i class="fa-solid fa-arrow-up"></i> Promote
                                                </a>
                                            <?php else: ?>
                                                <a href="?demote=<?php echo $user["id"]; ?>" onclick="return confirm('Are you sure you want to demote this admin to regular user?')" class="px-2 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 text-sm">
                                                    <i class="fa-solid fa-arrow-down"></i> Demote
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($user["approved"] == 0): ?>
                                                <a href="?approve=<?php echo $user["id"]; ?>" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="?delete=<?php echo $user["id"]; ?>" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </a>
                                            
                                            <a href="admin_edit_user.php?id=<?php echo $user["id"]; ?>" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                                <i class="fa-solid fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="flex justify-center mt-6">
                            <nav class="inline-flex rounded-md shadow">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-l-md hover:bg-gray-50">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>" 
                                       class="px-4 py-2 text-sm font-medium <?php echo $i === $page ? 'text-blue-700 bg-blue-100' : 'text-gray-700 bg-white hover:bg-gray-50'; ?> border border-gray-200">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-r-md hover:bg-gray-50">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fa-solid fa-search text-gray-400 text-5xl mb-4"></i>
                        <p class="text-lg text-gray-600">No users found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    // Confirm deletion
    document.querySelectorAll('a[href*="delete"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Confirm promotion
    document.querySelectorAll('a[href*="promote"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to promote this user to admin?')) {
                e.preventDefault();
            }
        });
    });
    
    // Confirm demotion
    document.querySelectorAll('a[href*="demote"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to demote this admin to regular user?')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>
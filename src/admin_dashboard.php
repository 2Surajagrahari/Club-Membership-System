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

// Fetch Admin Stats
$total_members = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$pending_approvals = $conn->query("SELECT COUNT(*) FROM users WHERE approved = 0")->fetch_row()[0];
$upcoming_events = $conn->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetch_row()[0];

// Payment Statistics
$total_payments = $conn->query("SELECT COUNT(*) FROM payments")->fetch_row()[0];
$total_revenue = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'completed'")->fetch_row()[0];
$pending_payments = $conn->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetch_row()[0];

// Fetch admin profile image
$admin_name = $_SESSION["user"];
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE name = ? AND role = 'admin'");
$stmt->bind_param("s", $admin_name);
$stmt->execute();
$stmt->bind_result($profile_image);
$stmt->fetch();
$stmt->close();

// Default profile image if none exists
if (empty($profile_image) || !file_exists($profile_image)) {
    $profile_image = "uploads/default.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Admin Dashboard | ClubSphere</title>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-red-800 to-red-900 text-white min-h-screen p-6">
            <h2 class="text-2xl font-bold mb-6"><a href="index.php">ClubSphere</a></h2>
            
            <!-- Admin Profile Section -->
            <div class="flex items-center space-x-3 mb-6">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile" class="w-12 h-12 rounded-full border-2 border-white">
                <div>
                    <p class="font-semibold"><?php echo $_SESSION["user"]; ?></p>
                    <p class="text-sm text-red-300">Administrator</p>
                </div>
            </div>

            <nav>
                <ul>
                    <li class="mb-4"><a href="admin_dashboard.php" class="block p-2 bg-white text-red-900 rounded"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                    <li class="mb-4"><a href="admin_users.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-users"></i> Manage Users</a></li>
                    <li class="mb-4"><a href="admin_approve.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user-check"></i> User Approvals <?php if($pending_approvals > 0): ?><span class="bg-red-500 text-white rounded-full px-2 ml-2"><?php echo $pending_approvals; ?></span><?php endif; ?></a></li>
                    <li class="mb-4"><a href="social.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-plus"></i> Post</a></li>
                    <li class="mb-4"><a href="event_planning.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-calendar-day"></i> Upcoming Events</a></li>
                    <li class="mb-4"><a href="finance.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-dollar-sign"></i> Finance</a></li>
                    <li class="mb-4"><a href="payment_records.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-credit-card"></i> Payment Records</a></li>
                    <li class="mb-4"><a href="design.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-images"></i> Posters</a></li>
                    <li class="mb-4"><a href="dashboard.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user"></i> User View</a></li>
                    <li class="mb-4"><a href="databases.php?logout=true" class="block p-2 bg-white text-red-900 rounded hover:bg-gray-200"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Top Header -->
            <div class="bg-gradient-to-r from-red-800 to-red-900 px-6 py-8 rounded-lg shadow-md mb-6">
                <h1 class="text-3xl font-bold text-white">Welcome back, <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'Admin'; ?>!</h1>
                <p class="text-red-200">Manage all aspects of ClubSphere from here</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Total Members</h2>
                    <p class="text-3xl font-semibold text-blue-600"><?php echo $total_members; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Pending Approvals</h2>
                    <p class="text-3xl font-semibold text-red-600"><?php echo $pending_approvals; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Total Revenue</h2>
                    <p class="text-3xl font-semibold text-green-600">$<?php echo number_format($total_revenue, 2); ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Pending Payments</h2>
                    <p class="text-3xl font-semibold text-yellow-600"><?php echo $pending_payments; ?></p>
                </div>
            </div>

            <!-- Recent Activity and Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Payments -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Recent Payments</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-red-800 text-white">
                                    <th class="p-3 text-left">Name</th>
                                    <th class="p-3 text-left">Method</th>
                                    <th class="p-3 text-left">Amount</th>
                                    <th class="p-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_payments = $conn->query("
                                    SELECT p.name, p.payment_method, p.amount, p.status, p.payment_date 
                                    FROM payments p
                                    ORDER BY p.payment_date DESC LIMIT 5
                                ");
                                while ($payment = $recent_payments->fetch_assoc()):
                                ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3"><?php echo htmlspecialchars($payment["name"]); ?></td>
                                    <td class="p-3">
                                        <?php 
                                        if ($payment["payment_method"] == 'qr') {
                                            echo '<span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">QR Payment</span>';
                                        } else {
                                            echo '<span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Card</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="p-3 font-semibold">$<?php echo number_format($payment["amount"], 2); ?></td>
                                    <td class="p-3">
                                        <?php if ($payment["status"] == 'completed'): ?>
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Completed</span>
                                        <?php elseif ($payment["status"] == 'pending'): ?>
                                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                        <?php else: ?>
                                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="payment_records.php" class="text-red-800 hover:underline">View all payments →</a>
                    </div>
                </div>

                <!-- Recent Activity and Quick Actions -->
<div class="grid grid-cols-1 gap-6">
    
        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="admin_approve.php" class="flex items-center justify-center p-4 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                    <i class="fa-solid fa-user-check mr-2"></i> Approve Users
                </a>
                <a href="event_planning.php" class="flex items-center justify-center p-4 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                    <i class="fa-solid fa-calendar-plus mr-2"></i> Manage Events
                </a>
                <a href="social.php" class="flex items-center justify-center p-4 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                    <i class="fa-solid fa-plus w-6"></i> Post
                </a>
                <a href="payment_records.php" class="flex items-center justify-center p-4 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">
                    <i class="fa-solid fa-credit-card mr-2"></i> Payment Records
                </a>
                <a href="finance.php" class="flex items-center justify-center p-4 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200">
                    <i class="fa-solid fa-dollar-sign mr-2"></i> Finance
                </a>
                <a href="design.php" class="flex items-center justify-center p-4 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200">
                    <i class="fa-solid fa-images mr-2"></i> Posters
                </a>
            </div>
        </div>
    </div>

    <!-- Second Row: Recent Users -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-700 mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-red-800 text-white">
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_users = $conn->query("SELECT id, name, email, approved FROM users ORDER BY id DESC LIMIT 5");
                    while ($user = $recent_users->fetch_assoc()):
                    ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3"><?php echo htmlspecialchars($user["name"]); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($user["email"]); ?></td>
                        <td class="p-3">
                            <?php if ($user["approved"] == 1): ?>
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Approved</span>
                            <?php else: ?>
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <a href="admin_users.php?action=view&id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($user["approved"] == 0): ?>
                                <a href="admin_approve.php?approve=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-check"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right">
            <a href="admin_users.php" class="text-red-800 hover:underline">View all users →</a>
        </div>
    </div>
</div>

                
            </div>
        </main>
    </div>
</body>
</html>
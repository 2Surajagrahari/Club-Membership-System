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
$pending_applications = $conn->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'pending'")->fetch_row()[0];

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
    <style>
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .hover-grow {
            transition: transform 0.3s ease;
        }
        .hover-grow:hover {
            transform: scale(1.03);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-red-800 to-red-900 text-white min-h-screen p-6">
            <h2 class="text-2xl font-bold mb-6"><a href="index.php">ClubSphere</a></h2>
            
            <!-- Admin Profile Section -->
            <div class="flex items-center space-x-3 mb-6">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile" class="w-12 h-12 rounded-full border-2 border-white object-cover">
                <div>
                    <p class="font-semibold"><?php echo $_SESSION["user"]; ?></p>
                    <p class="text-sm text-red-300">Administrator</p>
                </div>
            </div>

            <nav>
                <ul>
                    <li class="mb-4"><a href="admin_dashboard.php" class="flex items-center p-2 bg-white text-red-900 rounded hover:bg-gray-100"><i class="fa-solid fa-gauge mr-2"></i> Dashboard</a></li>
                    <li class="mb-4"><a href="admin_users.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-users mr-2"></i> Manage Users</a></li>
                    <li class="mb-4"><a href="admin_approve.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user-check mr-2"></i> User Approvals <?php if($pending_approvals > 0): ?><span class="bg-red-500 text-white rounded-full px-2 ml-auto"><?php echo $pending_approvals; ?></span><?php endif; ?></a></li>
                    <li class="mb-4"><a href="membership_applications.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-file-signature mr-2"></i> Applications <?php if($pending_applications > 0): ?><span class="bg-red-500 text-white rounded-full px-2 ml-auto"><?php echo $pending_applications; ?></span><?php endif; ?></a></li>
                    <li class="mb-4"><a href="social.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-plus mr-2"></i> Post</a></li>
                    <li class="mb-4"><a href="event_planning.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-calendar-day mr-2"></i> Events</a></li>
                    <li class="mb-4"><a href="finance.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-dollar-sign mr-2"></i> Finance</a></li>
                    <li class="mb-4"><a href="payment_records.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-credit-card mr-2"></i> Payments</a></li>
                    <li class="mb-4"><a href="design.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-images mr-2"></i> Posters</a></li>
                    <li class="mb-4"><a href="dashboard.php" class="flex items-center p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user mr-2"></i> User View</a></li>
                    <li class="mb-4"><a href="databases.php?logout=true" class="flex items-center p-2 bg-white text-red-900 rounded hover:bg-gray-200"><i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Top Header -->
            <div class="bg-gradient-to-r from-red-800 to-red-900 px-6 py-8 rounded-xl shadow-md mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Welcome back, <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'Admin'; ?>!</h1>
                        <p class="text-red-200">Manage all aspects of ClubSphere from here</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-white"><?php echo date('l, F j, Y'); ?></span>
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm"><?php echo date('h:i A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md hover-grow transition-all border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-700 mb-2">Total Members</h2>
                            <p class="text-3xl font-semibold text-blue-600"><?php echo $total_members; ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-md hover-grow transition-all border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-700 mb-2">Pending Approvals</h2>
                            <p class="text-3xl font-semibold text-yellow-600"><?php echo $pending_approvals; ?></p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-user-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-md hover-grow transition-all border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-700 mb-2">Total Revenue</h2>
                            <p class="text-3xl font-semibold text-green-600">$<?php echo number_format($total_revenue, 2); ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-md hover-grow transition-all border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-700 mb-2">New Applications</h2>
                            <p class="text-3xl font-semibold text-purple-600"><?php echo $pending_applications; ?></p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-file-signature text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Payments -->
                <div class="bg-white p-6 rounded-xl shadow-md lg:col-span-1">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-700">Recent Payments</h2>
                        <a href="viewallpay.php" class="text-sm text-red-800 hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php
                        $recent_payments = $conn->query("
                            SELECT p.name, p.payment_method, p.amount, p.status, p.payment_date 
                            FROM payments p
                            ORDER BY p.payment_date DESC LIMIT 5
                        ");
                        while ($payment = $recent_payments->fetch_assoc()):
                        ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 rounded-full <?php echo $payment["status"] == 'completed' ? 'bg-green-100 text-green-600' : ($payment["status"] == 'pending' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600'); ?>">
                                    <i class="fas fa-<?php echo $payment["payment_method"] == 'qr' ? 'qrcode' : 'credit-card'; ?>"></i>
                                </div>
                                <div>
                                    <p class="font-medium"><?php echo htmlspecialchars($payment["name"]); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('M j, g:i a', strtotime($payment["payment_date"])); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">$<?php echo number_format($payment["amount"], 2); ?></p>
                                <span class="text-xs px-2 py-1 rounded-full <?php echo $payment["status"] == 'completed' ? 'bg-green-100 text-green-800' : ($payment["status"] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($payment["status"]); ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="bg-white p-6 rounded-xl shadow-md lg:col-span-1">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-700">Recent Applications</h2>
                        <a href="membership_applications.php" class="text-sm text-red-800 hover:underline">View All</a>
                    </div>
                    <div class="space-y-4">
                        <?php
                        $recent_applications = $conn->query("
                            SELECT id, name, email, message, application_date, status 
                            FROM membership_applications 
                            ORDER BY application_date DESC LIMIT 5
                        ");
                        while ($app = $recent_applications->fetch_assoc()):
                        ?>
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer" onclick="window.location='view_application.php?id=<?php echo $app['id']; ?>'">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium"><?php echo htmlspecialchars($app["name"]); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('M j, g:i a', strtotime($app["application_date"])); ?></p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full <?php echo $app["status"] == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($app["status"] == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($app["status"]); ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 truncate"><?php echo htmlspecialchars($app["message"]); ?></p>
                            <div class="flex space-x-2 mt-3">
                                <a href="view_application.php?id=<?php echo $app['id']; ?>" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                <?php if ($app["status"] == 'pending'): ?>
                                <a href="process_application.php?action=approve&id=<?php echo $app['id']; ?>" class="text-xs text-green-600 hover:text-green-800">Approve</a>
                                <a href="process_application.php?action=reject&id=<?php echo $app['id']; ?>" class="text-xs text-red-600 hover:text-red-800">Reject</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white p-6 rounded-xl shadow-md lg:col-span-1">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="admin_approve.php" class="flex flex-col items-center justify-center p-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition border border-blue-100">
                            <i class="fa-solid fa-user-check text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-center">Approve Users</span>
                        </a>
                        <a href="membership_applications.php" class="flex flex-col items-center justify-center p-4 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition border border-purple-100">
                            <i class="fa-solid fa-file-signature text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-center">Review Applications</span>
                        </a>
                        <a href="event_planning.php" class="flex flex-col items-center justify-center p-4 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition border border-green-100">
                            <i class="fa-solid fa-calendar-plus text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-center">Create Event</span>
                        </a>
                        <a href="social.php" class="flex flex-col items-center justify-center p-4 bg-teal-50 text-teal-700 rounded-lg hover:bg-teal-100 transition border border-teal-100">
                            <i class="fa-solid fa-plus text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-center">New Post</span>
                        </a>
                        <a href="finance.php" class="flex flex-col items-center justify-center p-4 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition border border-emerald-100">
                            <i class="fa-solid fa-dollar-sign text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-center">Finance</span>
                        </a>
                        <a href="design.php" class="flex flex-col items-center justify-center p-4 bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition border border-amber-100">
                            <i class="fa-solid fa-images text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-center">Posters</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-700">Recent Users</h2>
                    <a href="admin_users.php" class="text-sm text-red-800 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-red-800 text-white">
                                <th class="p-3 text-left rounded-l-lg">Name</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left rounded-r-lg">Actions</th>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Approved
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <div class="flex space-x-2">
                                        <a href="admin_users.php?action=view&id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-800 p-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($user["approved"] == 0): ?>
                                            <a href="admin_approve.php?approve=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-800 p-1">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Simple animation for stats cards on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.hover-grow');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-pulse');
                    setTimeout(() => {
                        card.classList.remove('animate-pulse');
                    }, 1000);
                }, index * 200);
            });
        });
    </script>
</body>
</html>
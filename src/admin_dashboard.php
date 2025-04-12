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
$total_clubs = $conn->query("SELECT COUNT(*) FROM clubs")->fetch_row()[0] ?? 0;

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
            <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
            
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
        <li class="mb-4"><a href="admin_events.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-calendar"></i> Events</a></li>
        <li class="mb-4"><a href="admin_upcoming_events.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-calendar-day"></i> Upcoming Events</a></li>
        <li class="mb-4"><a href="admin_finance.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-dollar-sign"></i> Finance</a></li>
        <li class="mb-4"><a href="admin_posters.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-images"></i> Posters</a></li>
        <li class="mb-4"><a href="dashboard.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user"></i> User View</a></li>
        <li class="mb-4"><a href="databases.php?logout=true" class="block p-2 bg-white text-red-900 rounded hover:bg-gray-200"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Top Header -->
            <div class="bg-gradient-to-r from-red-800 to-red-900 px-6 py-8 rounded-lg shadow-md mb-6">
                <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
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
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Upcoming Events</h2>
                    <p class="text-3xl font-semibold text-green-600"><?php echo $upcoming_events; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold text-gray-700 mb-2">Total Clubs</h2>
                    <p class="text-3xl font-semibold text-purple-600"><?php echo $total_clubs; ?></p>
                </div>
            </div>

            <!-- Recent Activity and Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Users -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold text-gray-700 mb-4">Recent Users</h2>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-red-800 text-white">
                                <th class="p-3 text-left">Name</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_users = $conn->query("SELECT name, email, approved FROM users ORDER BY id DESC LIMIT 5");
                            while ($user = $recent_users->fetch_assoc()):
                            ?>
                            <tr class="border-b">
                                <td class="p-3"><?php echo $user["name"]; ?></td>
                                <td class="p-3"><?php echo $user["email"]; ?></td>
                                <td class="p-3">
                                    <?php if ($user["approved"] == 1): ?>
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Approved</span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold text-gray-700 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 gap-4">
        <a href="admin_approve.php" class="flex items-center justify-center p-4 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
            <i class="fa-solid fa-user-check mr-2"></i> Approve Users
        </a>
        <a href="admin_events.php" class="flex items-center justify-center p-4 bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
            <i class="fa-solid fa-calendar-plus mr-2"></i> Manage Events
        </a>
        <a href="admin_users.php" class="flex items-center justify-center p-4 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">
            <i class="fa-solid fa-users mr-2"></i> Manage Users
        </a>
        <a href="admin_finance.php" class="flex items-center justify-center p-4 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200">
            <i class="fa-solid fa-dollar-sign mr-2"></i> Finance Management
        </a>
        <a href="admin_posters.php" class="flex items-center justify-center p-4 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200">
            <i class="fa-solid fa-images mr-2"></i> Upload Posters
        </a>
        <a href="admin_upcoming_events.php" class="flex items-center justify-center p-4 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200">
            <i class="fa-solid fa-calendar-day mr-2"></i> Upcoming Events
        </a>
    </div>
</div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
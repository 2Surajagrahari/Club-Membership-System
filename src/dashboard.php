<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

// Enable error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch User Data
$user_email = $_SESSION["user"];
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($profile_image_path);
$stmt->fetch();
$stmt->close();

// Extract username from email if available
$username = strstr($user_email, '@', true);
if (!$username) {
    $username = $user_email;
}

// Default Profile Image
$default_image = "uploads/default.png";

// Determine which profile image to use
$profile_image = $default_image; // Start with default

if (!empty($profile_image_path)) {
    if (file_exists($profile_image_path)) {
        $profile_image = $profile_image_path; // Use user's image if it exists
    }
}

// Fetch Dashboard Stats
$total_members = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$stmt = $conn->prepare("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()");
$stmt->execute();
$stmt->bind_result($upcoming_events_count);
$stmt->fetch();
$stmt->close();

$unread_notifications = $conn->query("SELECT COUNT(*) FROM notifications WHERE status = 'unread'")->fetch_row()[0];

// Fetch upcoming events for the dashboard sidebar
$today = date('Y-m-d');
$events_sql = "SELECT id, event_name, event_date, event_location FROM events 
               WHERE event_date >= ? 
               ORDER BY event_date ASC 
               LIMIT 3"; // Limit to 3 most recent events
$events_stmt = $conn->prepare($events_sql);
$events_stmt->bind_param("s", $today);
$events_stmt->execute();
$events_result = $events_stmt->get_result();

// Sample activities data (no database query)
$activities = [
    ['activity_type' => 'Joined New Club', 'activity_date' => date('Y-m-d', strtotime('-1 day')), 'status' => 'completed'],
    ['activity_type' => 'Submitted Event Proposal', 'activity_date' => date('Y-m-d', strtotime('-2 days')), 'status' => 'pending'],
    ['activity_type' => 'Updated Profile', 'activity_date' => date('Y-m-d', strtotime('-5 days')), 'status' => 'completed']
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        .sidebar-item {
            @apply flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200;
        }
        
        .sidebar-item.active {
            @apply bg-white text-primary-700 font-medium;
        }
        
        .sidebar-item:not(.active) {
            @apply text-white hover:bg-primary-700 hover:text-white;
        }
        
        .card-stats {
            @apply relative overflow-hidden bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100;
        }
        
        .card-icon {
            @apply absolute top-4 right-4 text-4xl opacity-10;
        }
        
        .status-badge {
            @apply px-3 py-1 rounded-full text-xs font-semibold;
        }
        
        .status-completed {
            @apply bg-green-100 text-green-800;
        }
        
        .status-pending {
            @apply bg-yellow-100 text-yellow-800;
        }
        
        .status-cancelled {
            @apply bg-red-100 text-red-800;
        }
    </style>
    <title>Dashboard | ClubSphere</title>
</head>

<body class="bg-gray-50 font-sans min-h-screen">
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar for desktop -->
        <aside class="w-full md:w-64 bg-gradient-to-br from-primary-800 to-primary-900 text-white md:min-h-screen p-5 md:fixed">
            <div class="flex items-center justify-between md:justify-start md:space-x-3 mb-8">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-globe text-2xl text-primary-300"></i>
                    <h2 class="text-2xl font-bold">ClubSphere</h2>
                </div>
                <button id="mobile-menu-toggle" class="md:hidden text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Profile Section -->
            <div class="flex items-center space-x-3 mb-8 p-3 bg-primary-800/50 rounded-xl">
                <img src="<?php echo $profile_image; ?>" alt="Profile" class="w-12 h-12 rounded-full border-2 border-primary-300 object-cover">
                <div>
                    <p class="font-semibold"><?php echo $username; ?></p>
                    <a href="edit_profile.php" class="text-xs text-primary-300 hover:underline flex items-center space-x-1">
                        <span>Edit Profile</span>
                        <i class="fas fa-pen-to-square text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Navigation -->
            <nav id="mobile-menu" class="md:block">
                <ul class="space-y-2">
                    <li><a href="#" class="sidebar-item active"><i class="fa-solid fa-chart-line w-5"></i><span>Dashboard</span></a></li>
                    <li><a href="profile.php" class="sidebar-item"><i class="fa-solid fa-user w-5"></i><span>My Profile</span></a></li>
                    <li><a href="event_planning.php" class="sidebar-item"><i class="fa-solid fa-calendar-days w-5"></i><span>Events</span></a></li>
                    <li><a href="#" class="sidebar-item"><i class="fa-solid fa-users w-5"></i><span>Clubs</span></a></li>
                    <li><a href="#" class="sidebar-item"><i class="fa-solid fa-bell w-5"></i><span>Notifications</span></a></li>
                    <li><a href="#" class="sidebar-item"><i class="fa-solid fa-gear w-5"></i><span>Settings</span></a></li>
                    <li class="mt-8"><a href="index.php" class="sidebar-item bg-white/10"><i class="fa-solid fa-house w-5"></i><span>Back to Home</span></a></li>
                    <li><a href="databases.php?logout=true" class="sidebar-item bg-red-600 hover:bg-red-700 text-white"><i class="fa-solid fa-right-from-bracket w-5"></i><span>Logout</span></a></li>
                </ul>
            </nav>
            
            <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                <div class="mt-8">
                    <a href="admin_dashboard.php" class="flex items-center justify-center space-x-2 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition">
                        <i class="fas fa-lock"></i>
                        <span>Admin Dashboard</span>
                    </a>
                </div>
            <?php endif; ?>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 p-4 md:p-8">
            <!-- Welcome Section with Stats Overview -->
            <div class="bg-gradient-to-r from-primary-700 to-purple-600 rounded-2xl shadow-lg mb-8 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Welcome back, <?php echo $username; ?>!</h1>
                            <p class="text-primary-100 mb-4">Here's what's happening with your club activities</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg py-2 px-4 inline-flex items-center space-x-2">
                                <i class="fas fa-calendar text-white"></i>
                                <span class="text-white"><?php echo date('l, F j, Y'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats Cards Inside Banner -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-500 p-3 rounded-lg">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-100">Total Members</p>
                                    <h3 class="text-xl font-bold text-white"><?php echo $total_members; ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-purple-500 p-3 rounded-lg">
                                    <i class="fas fa-calendar-check text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-100">Upcoming Events</p>
                                    <h3 class="text-xl font-bold text-white"><?php echo $upcoming_events_count; ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-red-500 p-3 rounded-lg">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-100">Notifications</p>
                                    <h3 class="text-xl font-bold text-white"><?php echo $unread_notifications; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Feature Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card-stats group">
                    <i class="fas fa-user-plus card-icon text-blue-500"></i>
                    <h3 class="text-gray-500 mb-1 text-sm">My Clubs</h3>
                    <p class="text-2xl font-bold text-gray-800">5</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-green-600 text-xs font-semibold flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 12% 
                        </span>
                        <a href="#" class="text-primary-600 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                    </div>
                </div>
                
                <div class="card-stats group">
                    <i class="fas fa-calendar-alt card-icon text-purple-500"></i>
                    <h3 class="text-gray-500 mb-1 text-sm">Events Attended</h3>
                    <p class="text-2xl font-bold text-gray-800">12</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-green-600 text-xs font-semibold flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 8% 
                        </span>
                        <a href="#" class="text-primary-600 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                    </div>
                </div>
                
                <div class="card-stats group">
                    <i class="fas fa-certificate card-icon text-indigo-500"></i>
                    <h3 class="text-gray-500 mb-1 text-sm">Achievements</h3>
                    <p class="text-2xl font-bold text-gray-800">3</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-gray-500 text-xs font-semibold flex items-center">
                            <i class="fas fa-minus mr-1"></i> New badge soon
                        </span>
                        <a href="#" class="text-primary-600 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                    </div>
                </div>
                
                <div class="card-stats group">
                    <i class="fas fa-comments card-icon text-green-500"></i>
                    <h3 class="text-gray-500 mb-1 text-sm">Discussions</h3>
                    <p class="text-2xl font-bold text-gray-800">24</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-red-600 text-xs font-semibold flex items-center">
                            <i class="fas fa-arrow-down mr-1"></i> 3%
                        </span>
                        <a href="#" class="text-primary-600 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">View all →</a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activities and Upcoming Events -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Recent Activities -->
                <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Recent Activities</h2>
                        <a href="#" class="text-primary-600 text-sm font-medium hover:underline">View all</a>
                    </div>
                    
                    <div class="space-y-4">
                        <?php foreach ($activities as $index => $activity): ?>
                            <div class="flex items-start p-4 rounded-lg hover:bg-gray-50 transition-colors">
                                <?php
                                    // Determine icon and color based on activity type
                                    $iconClass = 'fas fa-check-circle';
                                    $iconColor = 'text-green-500';
                                    
                                    if (stripos($activity['activity_type'], 'joined') !== false) {
                                        $iconClass = 'fas fa-user-plus';
                                        $iconColor = 'text-blue-500';
                                    } elseif (stripos($activity['activity_type'], 'submitted') !== false) {
                                        $iconClass = 'fas fa-file-alt';
                                        $iconColor = 'text-indigo-500';
                                    } elseif (stripos($activity['activity_type'], 'updated') !== false) {
                                        $iconClass = 'fas fa-pen';
                                        $iconColor = 'text-orange-500';
                                    }
                                    
                                    // Determine status badge class
                                    $statusClass = 'status-pending';
                                    if ($activity['status'] === 'completed') {
                                        $statusClass = 'status-completed';
                                    } elseif ($activity['status'] === 'cancelled') {
                                        $statusClass = 'status-cancelled';
                                    }
                                ?>
                                
                                <div class="mr-4 mt-1">
                                    <span class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100">
                                        <i class="<?php echo $iconClass; ?> <?php echo $iconColor; ?>"></i>
                                    </span>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <h4 class="text-sm font-semibold text-gray-800"><?php echo $activity['activity_type']; ?></h4>
                                        <span class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($activity['activity_date'])); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">You have <?php echo strtolower($activity['activity_type']); ?></p>
                                    <span class="status-badge <?php echo $statusClass; ?> mt-2 inline-block">
                                        <?php echo ucfirst($activity['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Upcoming Events -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Upcoming Events</h2>
                        <a href="event_planning.php" class="text-primary-600 text-sm font-medium hover:underline">View All</a>
                    </div>
                    
                    <div class="space-y-4">
                        <?php 
                        // Array of border colors to cycle through
                        $borderColors = ['border-primary-500', 'border-green-500', 'border-yellow-500', 'border-red-500', 'border-blue-500'];
                        $colorIndex = 0;
                        
                        if ($events_result->num_rows > 0): 
                            while ($event = $events_result->fetch_assoc()): 
                                // Format date nicely
                                $event_date = new DateTime($event['event_date']);
                                $formatted_date = $event_date->format('M j, Y');
                                
                                // Cycle through colors
                                $borderColor = $borderColors[$colorIndex % count($borderColors)];
                                $colorIndex++;
                        ?>
                            <div class="border-l-4 <?php echo $borderColor; ?> pl-4 py-1 hover:bg-gray-50 transition-colors rounded-r">
                                <p class="text-xs text-gray-500"><?php echo $formatted_date; ?></p>
                                <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                                <div class="flex items-center mt-2">
                                    <i class="fas fa-map-marker-alt text-gray-400 text-xs mr-1"></i>
                                    <span class="text-sm text-gray-600"><?php echo htmlspecialchars($event['event_location']); ?></span>
                                </div>
                            </div>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <div class="text-center py-6 text-gray-500">
                                <i class="fas fa-calendar-alt text-4xl mb-2 opacity-30"></i>
                                <p>No upcoming events</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($events_result->num_rows > 0): ?>
                    <!-- View All Events Button -->
                    <a href="event_planning.php" class="block w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg mt-6 text-sm font-medium transition-colors text-center">
                        See All Events
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="mt-8 text-center text-gray-500 text-sm py-6">
                <p>© 2025 ClubSphere. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
        
        // Hide mobile menu on larger screens
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                document.getElementById('mobile-menu').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
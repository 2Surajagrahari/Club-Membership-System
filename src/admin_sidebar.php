<?php
// Database connection for sidebar-specific queries
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get counts for sidebar badges
$pending_approvals = $conn->query("SELECT COUNT(*) FROM users WHERE approved = 0")->fetch_row()[0];
$pending_applications = $conn->query("SELECT COUNT(*) FROM membership_applications WHERE status = 'pending'")->fetch_row()[0];

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

<aside class="w-64 bg-gradient-to-b from-red-800 to-red-900 text-white min-h-screen p-6 flex flex-col">
    <!-- Logo -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold"><a href="index.php" class="flex items-center">
            ClubSphere
        </a></h2>
    </div>
    
    <!-- Admin Profile -->
    <div class="flex items-center space-x-3 mb-6">
                <img src="<?php echo $profile_image; ?>" alt="Admin Profile" class="w-12 h-12 rounded-full border-2 border-white object-cover">
                <div>
                    <p class="font-semibold"><?php echo $_SESSION["user"]; ?></p>
                    <p class="text-sm text-red-300">Administrator</p>
                </div>
            </div>

    <!-- Main Navigation -->
    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href="admin_dashboard.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-gauge mr-3 w-5 text-center"></i>
                    Dashboard
                </a>
            </li>
            
            <li>
                <a href="admin_users.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    Manage Users
                </a>
            </li>
            
            <li>
                <a href="admin_approve.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_approve.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-user-check mr-3 w-5 text-center"></i>
                    User Approvals
                    <?php if($pending_approvals > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        <?php echo $pending_approvals; ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li>
                <a href="membership_applications.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'membership_applications.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-file-signature mr-3 w-5 text-center"></i>
                    Applications
                    <?php if($pending_applications > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                        <?php echo $pending_applications; ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li>
                <a href="event_planning.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'event_planning.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-calendar-day mr-3 w-5 text-center"></i>
                    Events
                </a>
            </li>
            
            <li>
                <a href="social.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'social.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-plus mr-3 w-5 text-center"></i>
                    Create Post
                </a>
            </li>
            
            <li>
                <a href="finance.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'finance.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-dollar-sign mr-3 w-5 text-center"></i>
                    Finance
                </a>
            </li>
            
            <li>
                <a href="payment_records.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'payment_records.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-credit-card mr-3 w-5 text-center"></i>
                    Payments
                </a>
            </li>
            
            <li>
                <a href="design.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition <?php echo basename($_SERVER['PHP_SELF']) == 'design.php' ? 'bg-white text-red-900' : ''; ?>">
                    <i class="fas fa-images mr-3 w-5 text-center"></i>
                    Posters
                </a>
            </li>
        </ul>
    </nav>

    <!-- Bottom Navigation -->
    <div class="mt-auto pt-4 border-t border-red-700">
        <ul class="space-y-2">
            <li>
                <a href="dashboard.php" 
                   class="flex items-center p-3 rounded-lg hover:bg-white hover:text-red-900 transition">
                    <i class="fas fa-user mr-3 w-5 text-center"></i>
                    User View
                </a>
            </li>
            
            <li>
                <a href="databases.php?logout=true" 
                   class="flex items-center p-3 rounded-lg bg-white bg-opacity-20 hover:bg-white hover:text-red-900 transition">
                    <i class="fas fa-right-from-bracket mr-3 w-5 text-center"></i>
                    Logout
                </a>
            </li>
        </ul>
        
        
    </div>
</aside>

<script>
// Make sidebar collapsible on mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.createElement('div');
    sidebarToggle.className = 'lg:hidden fixed bottom-4 right-4 bg-red-800 text-white p-3 rounded-full shadow-lg z-50';
    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.appendChild(sidebarToggle);
    
    const sidebar = document.querySelector('aside');
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
    });
    
    // Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && e.target !== sidebarToggle) {
            sidebar.classList.add('hidden');
        }
    });
});
</script>
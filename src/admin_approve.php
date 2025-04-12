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

// Approve user - FIXED to send email after approval
if (isset($_GET["approve"])) {
    $user_id = intval($_GET["approve"]);
    
    // Get user email first
    $result = $conn->query("SELECT email, name FROM users WHERE id = $user_id");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_email = $user["email"];
        $user_name = $user["name"];
        
        // Update user status
        if ($conn->query("UPDATE users SET approved = 1 WHERE id = $user_id")) {
            // Send confirmation email
            $subject = "ClubSphere Account Approved!";
            $message = "Hi $user_name,\n\nYour ClubSphere account has been approved. You can now log in.\n\nBest regards,\nClubSphere Team";
            $headers = "From: no-reply@clubsphere.com";
            
            mail($user_email, $subject, $message, $headers);
            
            // Set success message
            $_SESSION["success"] = "User approved and notification email sent";
        } else {
            $_SESSION["error"] = "Error approving user: " . $conn->error;
        }
    }
    
    header("Location: admin_approve.php");
    exit();
}

// Fetch unapproved users
$result = $conn->query("SELECT id, name, email, DATE_FORMAT(created_at, '%M %d, %Y') as signup_date FROM users WHERE approved = 0 ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>User Approvals | ClubSphere Admin</title>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <!-- Admin Sidebar (simplified - could be included from a common file) -->
        <aside class="w-64 bg-gradient-to-b from-red-800 to-red-900 text-white min-h-screen p-6">
            <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
            <nav>
                <ul>
                    <li class="mb-4"><a href="admin_dashboard.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                    <li class="mb-4"><a href="admin_approve.php" class="block p-2 bg-white text-red-900 rounded"><i class="fa-solid fa-user-check"></i> User Approvals</a></li>
                    <li class="mb-4"><a href="admin_users.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-users"></i> Manage Users</a></li>
                    <li class="mb-4"><a href="admin_events.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-calendar"></i> Manage Events</a></li>
                    <li class="mb-4"><a href="dashboard.php" class="block p-2 hover:bg-white hover:text-red-900 rounded"><i class="fa-solid fa-user"></i> User View</a></li>
                    <li class="mb-4"><a href="databases.php?logout=true" class="block p-2 bg-red-600 rounded hover:bg-red-700"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Top Header -->
            <div class="bg-gradient-to-r from-red-800 to-red-900 px-6 py-8 rounded-lg shadow-md mb-6">
                <h1 class="text-3xl font-bold text-white">Pending User Approvals</h1>
                <p class="text-red-200">Review and approve new user registrations</p>
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

            <!-- User Approval Table -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <?php if ($result->num_rows > 0): ?>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-red-800 text-white">
                                <th class="p-3 text-left">Name</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Signup Date</th>
                                <th class="p-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3"><?php echo htmlspecialchars($row["name"]); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row["email"]); ?></td>
                                    <td class="p-3"><?php echo $row["signup_date"] ?? 'N/A'; ?></td>
                                    <td class="p-3">
                                        <a href="?approve=<?php echo $row["id"]; ?>" class="inline-block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                            <i class="fa-solid fa-check mr-1"></i> Approve
                                        </a>
                                        <a href="?reject=<?php echo $row["id"]; ?>" class="inline-block px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 ml-2">
                                            <i class="fa-solid fa-times mr-1"></i> Reject
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fa-solid fa-check-circle text-green-500 text-5xl mb-4"></i>
                        <p class="text-lg text-gray-600">No pending approvals at this time.</p>
                        <a href="admin_dashboard.php" class="inline-block mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Back to Dashboard
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
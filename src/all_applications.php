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

// Fetch all applications
$applications = $conn->query("
    SELECT * FROM membership_applications 
    ORDER BY application_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applications | ClubSphere</title>
    <!-- Include your CSS links here -->
</head>
<body class="bg-gray-100">
    <!-- Include your sidebar/navigation here -->
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">All Membership Applications</h1>
            <div class="flex space-x-2">
                <a href="admin_dashboard.php" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-800 text-white">
                        <tr>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">Date</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $applications->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3"><?php echo htmlspecialchars($app['name']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($app['email']); ?></td>
                            <td class="p-3"><?php echo date('M j, Y', strtotime($app['application_date'])); ?></td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    <?php echo $app['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                          ($app['status'] == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                    <?php echo ucfirst($app['status']); ?>
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex space-x-2">
                                    <a href="view_application.php?id=<?php echo $app['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($app['status'] == 'pending'): ?>
                                    <a href="process_application.php?action=approve&id=<?php echo $app['id']; ?>" 
                                       class="text-green-600 hover:text-green-800" 
                                       title="Approve">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="process_application.php?action=reject&id=<?php echo $app['id']; ?>" 
                                       class="text-red-600 hover:text-red-800" 
                                       title="Reject">
                                        <i class="fas fa-times"></i>
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
</body>
</html>
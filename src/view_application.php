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

// Get application ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch application details
$stmt = $conn->prepare("SELECT * FROM membership_applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application) {
    die("Application not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application | ClubSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Membership Application</h1>
                <span class="px-4 py-2 rounded-full text-sm font-medium 
                    <?php echo $application['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                          ($application['status'] == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                    <?php echo ucfirst($application['status']); ?>
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Applicant Information</h2>
                    <div class="space-y-2">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($application['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                        <p><strong>Application Date:</strong> <?php echo date('F j, Y \a\t g:i a', strtotime($application['application_date'])); ?></p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Application Details</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="whitespace-pre-line"><?php echo htmlspecialchars($application['message']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <?php if ($application['status'] == 'pending'): ?>
                    <a href="process_application.php?action=approve&id=<?php echo $application['id']; ?>" 
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Approve Application
                    </a>
                    <a href="process_application.php?action=reject&id=<?php echo $application['id']; ?>" 
                       class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Reject Application
                    </a>
                <?php endif; ?>
                <a href="admin_dashboard.php" 
                   class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
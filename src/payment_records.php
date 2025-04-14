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

// Fetch payments
$payments = $conn->query("
    SELECT p.*, u.name as user_name 
    FROM payments p
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.payment_date DESC
");

// Fetch admin profile image (same as dashboard)
// ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Same head content as dashboard -->
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <!-- Same sidebar as dashboard -->
        
        <main class="flex-1 p-8">
            <div class="bg-gradient-to-r from-red-800 to-red-900 px-6 py-8 rounded-lg shadow-md mb-6">
                <h1 class="text-3xl font-bold text-white">Payment Records</h1>
                <p class="text-red-200">View all payment transactions</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-700">All Payments</h2>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-download mr-2"></i> Export
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-red-800 text-white">
                                <th class="p-3 text-left">Date</th>
                                <th class="p-3 text-left">User</th>
                                <th class="p-3 text-left">Method</th>
                                <th class="p-3 text-left">Amount</th>
                                <th class="p-3 text-left">Details</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3"><?php echo date('M d, Y h:i A', strtotime($payment['payment_date'])); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($payment['user_name'] ?? 'Guest'); ?></td>
                                <td class="p-3">
                                    <?php if ($payment['payment_method'] == 'qr'): ?>
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">QR Payment</span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Card</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 font-semibold">$<?php echo number_format($payment['amount'], 2); ?></td>
                                <td class="p-3 text-sm">
                                    <?php if ($payment['payment_method'] == 'qr'): ?>
                                        UTR: <?php echo $payment['utr_number'] ?? 'N/A'; ?>
                                    <?php else: ?>
                                        Card: ****<?php echo substr($payment['card_number'], -4); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <?php if ($payment['status'] == 'completed'): ?>
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Completed</span>
                                    <?php elseif ($payment['status'] == 'pending'): ?>
                                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <button class="text-blue-600 hover:text-blue-800 mr-2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] == 'pending'): ?>
                                        <button class="text-green-600 hover:text-green-800" title="Approve Payment">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
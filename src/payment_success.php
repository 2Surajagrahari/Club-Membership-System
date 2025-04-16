<?php
// Start session and check if payment data exists
session_start();
if (!isset($_SESSION['payment_receipt'])) {
    header("Location: pay.php");
    exit();
}

$receipt = $_SESSION['payment_receipt'];
unset($_SESSION['payment_receipt']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md text-center">
        <div class="mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful!</h1>
            <p class="text-gray-600">Thank you for your payment of <span class="font-semibold">$<?php echo number_format($receipt['amount'], 2); ?></span></p>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-lg mb-6 text-left">
            <h2 class="font-semibold text-gray-800 mb-3">Payment Details</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Transaction ID:</span>
                    <span class="font-medium"><?php echo $receipt['transaction_id']; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Invoice Number:</span>
                    <span class="font-medium"><?php echo $receipt['invoice_number']; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-medium"><?php echo ucfirst($receipt['payment_method']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium"><?php echo $receipt['payment_date']; ?></span>
                </div>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-600 mb-4">A receipt has been sent to <span class="font-semibold"><?php echo $receipt['email']; ?></span></p>
            <button onclick="window.print()" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold hover:bg-indigo-700 transition flex items-center justify-center space-x-2">
                <i class="fas fa-print"></i>
                <span>Print Receipt</span>
            </button>
        </div>
        
        <div>
            <a href="index.php" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center justify-center space-x-2">
                <i class="fas fa-home"></i>
                <span>Return to Home</span>
            </a>
        </div>
    </div>
</body>
</html>
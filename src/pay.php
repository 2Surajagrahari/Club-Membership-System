<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Now</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <!-- Go Back Button -->
    <div class="absolute top-5 left-5">
        <a href="index.php" 
           class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-full shadow-md hover:scale-105 transition-transform duration-300 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i> 
            <span>Go Back</span>
        </a>
    </div>
        <!-- Payment Logos -->
        <div class="flex justify-center space-x-4 mb-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" class="h-8">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="MasterCard" class="h-8">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-8">
        </div>

        <!-- Header -->
        <h2 class="text-2xl font-bold text-center text-indigo-500">Dues Payment</h2>
        <p class="text-center text-gray-500 mb-6">Secure your payment easily.</p>

        <!-- Payment Form -->
        <form action="payment-process.php" method="POST">
            <!-- Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Full Name</label>
                <input type="text" name="name" placeholder="Enter your name" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Email</label>
                <input type="email" name="email" placeholder="Enter your email" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Amount -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Amount (USD)</label>
                <input type="number" name="amount" placeholder="Enter amount" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Card Details -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Card Number</label>
                <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Expiry & CVV -->
            <div class="flex space-x-4 mb-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 font-medium">Expiry Date</label>
                    <input type="text" name="expiry" placeholder="MM/YY" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 font-medium">CVV</label>
                    <input type="password" name="cvv" placeholder="***" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>
            </div>

            <!-- Pay Now Button -->
            <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-md font-semibold shadow-md hover:scale-105 hover:shadow-lg transition-transform duration-300 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-credit-card text-lg"></i>
                <span>Pay Now</span>
            </button>
        </form>

        <!-- Security Notice -->
        <p class="mt-4 text-center text-gray-500 text-sm">
            <i class="fa-solid fa-lock text-purple-600"></i> Your payment is secure and encrypted.
        </p>
    </div>

</body>
</html>

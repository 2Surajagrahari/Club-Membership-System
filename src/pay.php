<?php
session_start();
require_once 'databases.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Now</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            500: '#6366f1',
                            600: '#4f46e5',
                        },
                        blue: {
                            500: '#3b82f6',
                            600: '#2563eb',
                        },
                        purple: {
                            600: '#9333ea',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <!-- Go Back Button -->
    <div class="absolute top-5 left-5">
        <a href="index.php" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-full shadow-md hover:scale-105 transition-transform duration-300 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i> 
            <span>Go Back</span>
        </a>
    </div>
    
    <!-- Payment Form Container -->
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md relative">
        <?php if (isset($_SESSION['payment_error'])): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php echo $_SESSION['payment_error']; ?>
                <?php unset($_SESSION['payment_error']); ?>
            </div>
        <?php endif; ?>

        <!-- Payment Method Tabs -->
        <div class="flex border-b mb-6">
            <button id="card-tab" class="flex-1 py-2 font-medium text-center border-b-2 border-indigo-500 text-indigo-600">
                Card Payment
            </button>
            <button id="qr-tab" class="flex-1 py-2 font-medium text-center text-gray-500">
                QR Payment
            </button>
        </div>

        <!-- Card Payment Form (Default Visible) -->
        <div id="card-form">
            <!-- Payment Logos -->
            <div class="flex justify-center space-x-4 mb-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" class="h-8">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="MasterCard" class="h-8">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-8">
            </div>

            <!-- Header -->
            <h2 class="text-2xl font-bold text-center text-indigo-500">Dues Payment</h2>
            <p class="text-center text-gray-500 mb-6">Secure card payment</p>

            <form action="databases.php" method="POST">
                <input type="hidden" name="payment_method" value="card">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required 
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required 
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- Amount -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Amount (USD)</label>
                    <input type="number" name="amount" placeholder="Enter amount" required min="1" step="0.01"
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- Card Details -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Card Number</label>
                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
                           required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300"
                           pattern="\d{16}" title="Please enter a valid 16-digit card number">
                </div>

                <!-- Expiry & CVV -->
                <div class="flex space-x-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 font-medium">Expiry Date</label>
                        <input type="text" name="expiry" placeholder="MM/YY" 
                               required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300"
                               pattern="(0[1-9]|1[0-2])\/\d{2}" title="Please enter a valid expiry date in MM/YY format">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 font-medium">CVV</label>
                        <input type="password" name="cvv" placeholder="***" 
                               required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300"
                               pattern="\d{3,4}" title="Please enter a 3 or 4-digit CVV">
                    </div>
                </div>

                <!-- Pay Now Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-md font-semibold shadow-md hover:scale-105 hover:shadow-lg transition-transform duration-300 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-credit-card text-lg"></i>
                    <span>Pay Now</span>
                </button>
            </form>
        </div>

        <!-- QR Payment Form (Initially Hidden) -->
        <div id="qr-form" class="hidden">
            <!-- Header -->
            <h2 class="text-2xl font-bold text-center text-indigo-500">QR Payment</h2>
            <p class="text-center text-gray-500 mb-6">Scan the QR code to pay</p>

            <!-- QR Code Display -->
            <div class="flex justify-center mb-6">
                <div class="border-2 border-dashed border-indigo-300 p-4 rounded-lg bg-indigo-50">
                    <img src="QR.jpeg" alt="Payment QR Code" class="w-48 h-48 mx-auto">
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">Payment Instructions:</h3>
                <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                    <li>Open your banking/payment app</li>
                    <li>Scan the QR code above</li>
                    <li>Enter the amount shown below</li>
                    <li>Complete the payment</li>
                    <li>Enter your UTR number below</li>
                </ol>
            </div>

            <form action="databases.php" method="POST">
                <input type="hidden" name="payment_method" value="qr">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required 
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required 
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- Amount -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Amount (USD)</label>
                    <input type="number" name="amount" placeholder="Enter amount" required min="1" step="0.01"
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                </div>

                <!-- UTR Number -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">UTR/Transaction Number</label>
                    <input type="text" name="utr_number" placeholder="Enter your UTR number" required 
                           class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
                    <p class="text-xs text-gray-500 mt-1">Found in your payment receipt or bank statement</p>
                </div>

                <!-- Submit Payment -->
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-md font-semibold shadow-md hover:scale-105 hover:shadow-lg transition-transform duration-300 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-check-circle text-lg"></i>
                    <span>Confirm Payment</span>
                </button>   
            </form>
        </div>

        <!-- Security Notice -->
        <p class="mt-4 text-center text-gray-500 text-sm">
            <i class="fa-solid fa-lock text-indigo-600"></i> All payments are secure and encrypted
        </p>
    </div>

    <script>
        // Tab switching functionality
        const cardTab = document.getElementById('card-tab');
        const qrTab = document.getElementById('qr-tab');
        const cardForm = document.getElementById('card-form');
        const qrForm = document.getElementById('qr-form');

        cardTab.addEventListener('click', () => {
            cardTab.classList.add('border-indigo-500', 'text-indigo-600');
            cardTab.classList.remove('text-gray-500');
            qrTab.classList.remove('border-indigo-500', 'text-indigo-600');
            qrTab.classList.add('text-gray-500');
            cardForm.classList.remove('hidden');
            qrForm.classList.add('hidden');
        });

        qrTab.addEventListener('click', () => {
            qrTab.classList.add('border-indigo-500', 'text-indigo-600');
            qrTab.classList.remove('text-gray-500');
            cardTab.classList.remove('border-indigo-500', 'text-indigo-600');
            cardTab.classList.add('text-gray-500');
            qrForm.classList.remove('hidden');
            cardForm.classList.add('hidden');
        });

        // Auto-format card number
        document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/\s/g, '').replace(/(\d{4})/g, '$1 ').trim();
        });

        // Auto-format expiry date
        document.querySelector('input[name="expiry"]')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').replace(/(\d{2})(\d)/, '$1/$2');
        });
    </script>
</body>
</html>
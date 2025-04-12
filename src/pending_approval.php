<?php
session_start();
// Redirect if not logged in or already approved (role is set)
if (!isset($_SESSION["user"]) || isset($_SESSION["role"]) && $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Account Pending Approval | ClubSphere</title>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex items-center justify-center min-h-screen p-5">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <i class="fa-solid fa-clock text-yellow-500 text-5xl mb-4"></i>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Account Pending Approval</h1>
            <p class="text-gray-600 mb-6">Thank you for registering with ClubSphere! Your account is currently pending administrator approval.</p>
            <p class="text-gray-600 mb-6">You will receive an email notification once your account has been approved.</p>
            <div class="mt-8">
                <a href="index.php" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700">
                    <i class="fa-solid fa-home mr-2"></i> Return to Home
                </a>
                <a href="databases.php?logout=true" class="inline-block px-6 py-3 ml-4 bg-gray-500 text-white font-semibold rounded-lg shadow-md hover:bg-gray-600">
                    <i class="fa-solid fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</body>
</html>
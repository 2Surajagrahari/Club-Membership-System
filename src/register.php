<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <!-- Go Back Button -->
        <nav class="bg-blue-600 text-white p-4 shadow-lg">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-3xl font-bold">ClubSphere</h1>
                <a href="index.php" class="text-lg bg-white text-blue-600 px-4 py-2 rounded-md font-semibold hover:bg-gray-100 transition">Back to Home</a>
            </div>
        </nav>
        <!-- Event Details -->
        <h2 class="text-2xl font-bold text-center text-indigo-600">Register for the Event</h2>
        <p class="text-center text-gray-500 mb-6">Secure your spot now!</p>

        <!-- Registration Form -->
        <form id="registrationForm">
            <!-- Full Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Full Name</label>
                <input type="text" id="name" placeholder="Mr. Suraj Agrahari" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Email</label>
                <input type="email" id="email" placeholder="suraj@gmail.com" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Phone Number</label>
                <input type="tel" id="phone" placeholder="Enter your phone number" required class="w-full p-3 border rounded-md focus:ring-2 focus:ring-blue-300">
            </div>

            <!-- Register Button -->
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold shadow-md hover:scale-105 hover:shadow-lg transition-transform duration-300 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-user-plus text-lg"></i>
                <span>Register Now</span>
            </button>
        </form>

        <!-- Success Message (Hidden Initially) -->
        <p id="successMessage" class="mt-4 text-green-600 text-center font-semibold hidden">
            🎉 Registration Successful! Check your email for details.
        </p>
    </div>

    <script>
        document.getElementById("registrationForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission

            // Show success message
            document.getElementById("successMessage").classList.remove("hidden");

            // Clear the form
            document.getElementById("registrationForm").reset();
        });
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <title>ClubSphere</title>

    <style>
        /* Preloader Styling */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 1000;
            transition: opacity 0.5s ease-out;
        }
        .spin {
            width: 15px;
            height: 15px;
            margin: 5px;
            border-radius: 50%;
            background-color: #3498db;
            animation: bounce 1.5s infinite ease-in-out;
        }
        .spin1 { animation-delay: -0.3s; }
        .spin2 { animation-delay: -0.2s; }
        .spin3 { animation-delay: -0.1s; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Fade-out effect for Preloader */
        .fade-out {
            opacity: 0;
            visibility: hidden;
        }

        /* Fade-in transition */
        .fade-in {
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body >

    <!-- Preloader -->
    <div id="preloader">
        <div class="flex">
            <div class="spin spin1"></div>
            <div class="spin spin2"></div>
            <div class="spin spin3"></div>
        </div>
        <h1 class="mt-4 text-xl font-bold text-blue-600 animate-bounce">ClubSphere</h1>
    </div>

    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">ClubSphere</h1>
            <a href="index.php" class="text-lg bg-white text-blue-600 px-4 py-2 rounded-md font-semibold hover:bg-gray-100 transition">Back to Home</a>
        </div>
    </nav>
<div class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-6">




    <!-- Login / Signup Form Container -->
    <div class="bg-white p-8 rounded-lg shadow-2xl w-96 text-center transition-transform transform hover:scale-105">

        <!-- Login Form -->
<div id="loginForm" class="fade-in">
    <h2 class="text-3xl font-bold mb-4 text-blue-700">Login</h2>
    <form action="databases.php" method="POST">

        <div class="relative mb-3" id="emailInput">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 border rounded-full shadow-sm focus:ring-2 focus:ring-blue-300">
            <i class="fas fa-envelope absolute right-4 top-4 text-gray-400"></i>
        </div>

        <div class="relative mb-3">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded-full shadow-sm focus:ring-2 focus:ring-blue-300">
            <i class="fas fa-lock absolute right-4 top-4 text-gray-400"></i>
        </div>

        <button type="submit" class="bg-blue-600 text-white w-full p-3 rounded-full shadow-md hover:bg-blue-700 transition duration-300" name="login" onclick="return validateEmail()">Login</button>
        <p id="error-message" class="text-red-500 mt-2 hidden"></p>

    </form>

    <p class="mt-4 text-gray-600">Don't have an account? 
        <a href="#" onclick="toggleForm()" class="text-blue-600 hover:underline">Sign Up</a>
    </p>
</div>


<!-- Sign Up Form -->
<div id="signUpForm" class="hidden fade-in">
    <h2 class="text-3xl font-bold mb-4 text-green-700">Sign Up</h2>
    <form action="databases.php" method="POST">
        <div class="relative mb-3">
            <input type="text" name="name" placeholder="Full Name" required class="w-full p-3 border rounded-full shadow-sm focus:ring-2 focus:ring-green-300">
            <i class="fas fa-user absolute right-4 top-4 text-gray-400"></i>
        </div>

        <div class="relative mb-3">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 border rounded-full shadow-sm focus:ring-2 focus:ring-green-300">
            <i class="fas fa-envelope absolute right-4 top-4 text-gray-400"></i>
        </div>

        <div class="relative mb-3">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded-full shadow-sm focus:ring-2 focus:ring-green-300">
            <i class="fas fa-lock absolute right-4 top-4 text-gray-400"></i>
        </div>

        <!-- Add Role Selection (Admin or User) -->
        <div class="relative mb-3">
            <label for="role" class="text-gray-600">Select Role</label>
            <select name="role" class="w-full p-3 border rounded-full shadow-sm focus:ring-2 focus:ring-green-300">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="bg-green-600 text-white w-full p-3 rounded-full shadow-md hover:bg-green-700 transition duration-300" name="register">Sign Up</button>
    </form>

    <p class="mt-4 text-gray-600">Already have an account? 
        <a href="#" onclick="toggleForm()" class="text-blue-600 hover:underline">Login</a>
    </p>
</div>

    </div>
</div>
    <script>
        function toggleForm() {
            document.getElementById("loginForm").classList.toggle("hidden");
            document.getElementById("signUpForm").classList.toggle("hidden");
        }

        // Preloader Hide After Load
        window.onload = function() {
            document.getElementById("preloader").classList.add("fade-out");
            setTimeout(() => document.getElementById("preloader").style.display = "none", 500);
        };

        function validateEmail() {
            const emailInput = document.querySelector('input[name="email"]');
            const errorMessage = document.getElementById("error-message");
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(emailInput.value)) {
                errorMessage.textContent = "Please enter a valid email address.";
                errorMessage.classList.remove("hidden");
                return false;
            }
            errorMessage.classList.add("hidden");
            return true;
        }
    </script>

</body>
</html>
